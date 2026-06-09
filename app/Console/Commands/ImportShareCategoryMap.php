<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\CategoryMapping;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ImportShareCategoryMap extends Command
{
    protected $signature = 'excel:import-share-category-map
        {--path=public/share.xlsx : Relative path to the .xlsx file}
        {--dry-run : Do not write to DB}
        {--limit-sheets=0 : Limit number of sheets to process (0 = all)}
        {--sheets=* : Explicit sheet names to process}
        {--deactivate-others : Deactivate all categories not present in Excel tree}';

    protected $description = 'Import visible categories from share.xlsx and map them to source categories for product display';

    public function handle(): int
    {
        $relativePath = (string) $this->option('path');
        $isDryRun = (bool) $this->option('dry-run');
        $limitSheets = max(0, (int) $this->option('limit-sheets'));
        $explicitSheetNames = array_values(array_filter((array) $this->option('sheets'), static fn ($value) => $value !== null && $value !== ''));
        $shouldDeactivateOthers = (bool) $this->option('deactivate-others');

        $fullPath = base_path($relativePath);

        if (! is_file($fullPath)) {
            $this->error("File not found: {$fullPath}");

            return self::FAILURE;
        }

        $this->info("Loading: {$relativePath}");
        if ($isDryRun) {
            $this->warn('DRY RUN: no database changes will be made.');
        }

        if ($shouldDeactivateOthers && $isDryRun) {
            $this->warn('Note: --deactivate-others will not deactivate anything in --dry-run mode.');
        }

        try {
            $spreadsheet = IOFactory::load($fullPath);
        } catch (\Throwable $e) {
            $this->error('Failed to read Excel file.');
            $this->line($e->getMessage());

            return self::FAILURE;
        }

        $sheetNames = $spreadsheet->getSheetNames();
        if ($sheetNames === []) {
            $this->warn('No sheets found.');

            return self::SUCCESS;
        }

        $selectedSheetNames = $this->selectSheetNames($sheetNames, $explicitSheetNames, $limitSheets);
        if ($selectedSheetNames === []) {
            $this->warn('No sheets selected.');

            return self::SUCCESS;
        }

        $stats = [
            'sheets_total' => count($sheetNames),
            'sheets_selected' => count($selectedSheetNames),
            'level1_created' => 0,
            'level1_existing' => 0,
            'level2_created' => 0,
            'level2_existing' => 0,
            'rows_total' => 0,
            'rows_skipped' => 0,
            'source_matched' => 0,
            'source_ambiguous' => 0,
            'source_not_found' => 0,
            'mappings_created' => 0,
            'mappings_existing' => 0,
            'categories_deactivated' => 0,
        ];

        /** @var array<int, int> $visibleCategoryIds */
        $visibleCategoryIds = [];

        $run = function () use (
            $spreadsheet,
            $selectedSheetNames,
            $isDryRun,
            &$stats,
            &$visibleCategoryIds
        ): void {
            foreach ($selectedSheetNames as $sheetName) {
                $sheet = $spreadsheet->getSheetByName($sheetName);
                if (! $sheet instanceof Worksheet) {
                    $this->warn("Sheet not found (skipped): {$sheetName}");

                    continue;
                }

                $visibleLevel1 = $this->firstLevelCategoryForSheet($sheetName, $isDryRun, $stats);
                if ($visibleLevel1->exists) {
                    $visibleCategoryIds[] = $visibleLevel1->id;
                }

                $highestRow = (int) $sheet->getHighestDataRow();
                if ($highestRow < 2) {
                    continue;
                }

                $this->line("Processing sheet: {$sheetName}");

                for ($row = 2; $row <= $highestRow; $row++) {
                    $stats['rows_total']++;

                    $visibleTitle = $this->normalizeCellValue($sheet->getCell("A{$row}")->getValue());
                    $path = $this->normalizeCellValue($sheet->getCell("B{$row}")->getValue());

                    if ($visibleTitle === null || $path === null) {
                        $stats['rows_skipped']++;

                        continue;
                    }

                    $visibleLevel2 = $this->secondLevelVisibleCategory($visibleLevel1, $visibleTitle, $isDryRun, $stats);
                    if ($visibleLevel2->exists) {
                        $visibleCategoryIds[] = $visibleLevel2->id;
                    }

                    $match = $this->findSecondLevelCategoryByPathSuffix($path);
                    if (! $match['category']) {
                        $stats['source_not_found']++;
                        $this->warn("Source L2 not found. Sheet={$sheetName}, row={$row}, visible=\"{$visibleTitle}\", path=\"{$path}\"");

                        continue;
                    }

                    $stats['source_matched']++;
                    if ($match['ambiguous']) {
                        $stats['source_ambiguous']++;
                        $this->warn("Ambiguous source L2 match (multiple). Using first. Sheet={$sheetName}, row={$row}, matchedTitle=\"{$match['matched_title']}\"");
                    }

                    /** @var Category $sourceCategory */
                    $sourceCategory = $match['category'];

                    if ($isDryRun) {
                        if ($visibleLevel2->exists) {
                            $exists = CategoryMapping::query()
                                ->where('visible_category_id', $visibleLevel2->id)
                                ->where('source_category_id', $sourceCategory->id)
                                ->exists();

                            if ($exists) {
                                $stats['mappings_existing']++;

                                continue;
                            }

                            $stats['mappings_created']++;
                            $this->line("Would map visible \"{$visibleLevel2->title}\" (#{$visibleLevel2->id}) -> source \"{$sourceCategory->title}\" (#{$sourceCategory->id})");

                            continue;
                        }

                        $stats['mappings_created']++;
                        $this->line("Would create visible \"{$visibleLevel2->title}\" and map -> source \"{$sourceCategory->title}\" (#{$sourceCategory->id})");

                        continue;
                    }

                    $mapping = CategoryMapping::firstOrCreate([
                        'visible_category_id' => $visibleLevel2->id,
                        'source_category_id' => $sourceCategory->id,
                    ]);

                    if ($mapping->wasRecentlyCreated) {
                        $stats['mappings_created']++;
                    } else {
                        $stats['mappings_existing']++;
                    }
                }
            }
        };

        if ($isDryRun) {
            $run();
        } else {
            DB::transaction(function () use ($run): void {
                $run();
            });
        }

        $visibleCategoryIds = array_values(array_unique($visibleCategoryIds));

        if ($shouldDeactivateOthers && ! $isDryRun) {
            $stats['categories_deactivated'] = Category::query()
                ->whereNotIn('id', $visibleCategoryIds)
                ->where('is_active', true)
                ->update(['is_active' => false]);

            $this->info('Deactivated categories not present in Excel tree.');
        }

        $this->newLine();
        $this->table(
            ['Metric', 'Count'],
            [
                ['Sheets (total)', $stats['sheets_total']],
                ['Sheets (selected)', $stats['sheets_selected']],
                ['L1 created', $stats['level1_created']],
                ['L1 existing', $stats['level1_existing']],
                ['L2 created', $stats['level2_created']],
                ['L2 existing', $stats['level2_existing']],
                ['Rows total', $stats['rows_total']],
                ['Rows skipped', $stats['rows_skipped']],
                ['Source matched', $stats['source_matched']],
                ['Source ambiguous', $stats['source_ambiguous']],
                ['Source not found', $stats['source_not_found']],
                ['Mappings created', $stats['mappings_created']],
                ['Mappings existing', $stats['mappings_existing']],
                ['Categories deactivated', $stats['categories_deactivated']],
            ]
        );

        Log::info(
            'excel:import-share-category-map summary',
            array_merge($stats, ['command' => 'excel:import-share-category-map'])
        );

        return self::SUCCESS;
    }

    /**
     * @param  array<int, string>  $sheetNames
     * @param  array<int, string>  $explicitSheetNames
     * @return array<int, string>
     */
    private function selectSheetNames(array $sheetNames, array $explicitSheetNames, int $limitSheets): array
    {
        if ($explicitSheetNames !== []) {
            $missing = array_values(array_diff($explicitSheetNames, $sheetNames));
            foreach ($missing as $name) {
                $this->warn("Requested sheet not found: {$name}");
            }

            return array_values(array_intersect($explicitSheetNames, $sheetNames));
        }

        if ($limitSheets > 0) {
            return array_slice($sheetNames, 0, $limitSheets);
        }

        return $sheetNames;
    }

    private function firstLevelCategoryForSheet(string $sheetName, bool $isDryRun, array &$stats): Category
    {
        $existing = Category::query()
            ->whereNull('parent_id')
            ->where('level', 1)
            ->where('title', $sheetName)
            ->first();

        if ($existing instanceof Category) {
            if (! $existing->is_active && ! $isDryRun) {
                $existing->is_active = true;
                $existing->save();
            }

            $stats['level1_existing']++;

            return $existing;
        }

        $payload = [
            'title' => $sheetName,
            'slug' => Str::slug($sheetName),
            'is_active' => true,
            'parent_id' => null,
        ];

        if ($isDryRun) {
            $stats['level1_created']++;

            return new Category($payload);
        }

        $stats['level1_created']++;

        return Category::create($payload);
    }

    private function secondLevelVisibleCategory(Category $visibleLevel1, string $visibleTitle, bool $isDryRun, array &$stats): Category
    {
        $existing = Category::query()
            ->where('parent_id', $visibleLevel1->id)
            ->where('level', 2)
            ->where('title', $visibleTitle)
            ->first();

        if ($existing instanceof Category) {
            if (! $existing->is_active && ! $isDryRun) {
                $existing->is_active = true;
                $existing->save();
            }

            $stats['level2_existing']++;

            return $existing;
        }

        $payload = [
            'title' => $visibleTitle,
            'slug' => Str::slug($visibleTitle),
            'is_active' => true,
            'parent_id' => $visibleLevel1->id,
        ];

        if ($isDryRun) {
            $stats['level2_created']++;

            return new Category($payload);
        }

        $stats['level2_created']++;

        return Category::create($payload);
    }

    private function normalizeCellValue(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $string = trim((string) $value);
        $string = preg_replace('/\s+/u', ' ', $string) ?? $string;

        return $string === '' ? null : $string;
    }

    /**
     * @return array{category:?Category,matched_title:?string,ambiguous:bool}
     */
    private function findSecondLevelCategoryByPathSuffix(string $path): array
    {
        $words = preg_split('/\s+/u', trim($path)) ?: [];
        $words = array_values(array_filter($words, static fn ($w) => $w !== ''));

        $best = null;
        $bestTitle = null;
        $ambiguous = false;

        for ($n = 1; $n <= count($words); $n++) {
            $candidateTitle = implode(' ', array_slice($words, -$n));

            $query = Category::query()
                ->where('level', 2)
                ->where('title', $candidateTitle)
                ->orderBy('id');

            $count = (clone $query)->count();
            $match = $query->first();

            if ($match instanceof Category) {
                $best = $match;
                $bestTitle = $candidateTitle;
                $ambiguous = $count > 1;
                break;
            }
        }

        return [
            'category' => $best,
            'matched_title' => $bestTitle,
            'ambiguous' => $ambiguous,
        ];
    }
}
