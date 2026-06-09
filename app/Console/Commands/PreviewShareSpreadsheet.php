<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PreviewShareSpreadsheet extends Command
{
    protected $signature = 'excel:preview-share
        {--path=public/share.xlsx : Relative path to the .xlsx file}
        {--rows=10 : Number of rows to print per sheet}
        {--first-sheets=3 : Number of first sheets to print (by order)}
        {--sheets=* : Explicit sheet names to print (overrides --first-sheets)}
        {--log-sheet= : Log a single sheet by name}
        {--log-rows=50 : Number of rows to log when using --log-sheet}';

    protected $description = 'Preview sheets from share.xlsx (print first rows)';

    public function handle(): int
    {
        $relativePath = (string) $this->option('path');
        $rowsToPrint = max(0, (int) $this->option('rows'));
        $firstSheetsToPrint = max(0, (int) $this->option('first-sheets'));
        $explicitSheetNames = array_values(array_filter((array) $this->option('sheets'), static fn ($value) => $value !== null && $value !== ''));
        $logSheetName = $this->option('log-sheet');
        $rowsToLog = max(0, (int) $this->option('log-rows'));

        $fullPath = base_path($relativePath);

        if (! is_file($fullPath)) {
            $this->error("File not found: {$fullPath}");

            return self::FAILURE;
        }

        $this->info("Loading: {$relativePath}");

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

        $this->info('Sheets found:');
        foreach ($sheetNames as $index => $name) {
            $this->line(sprintf('  [%d] %s', $index, $name));
        }
        $this->newLine();

        if (is_string($logSheetName) && $logSheetName !== '') {
            $sheet = $spreadsheet->getSheetByName($logSheetName);

            if (! $sheet instanceof Worksheet) {
                $this->error("Sheet not found: {$logSheetName}");

                return self::FAILURE;
            }

            $this->logSheetPreview($relativePath, $sheet, $rowsToLog);
            $this->info("Logged sheet \"{$logSheetName}\" to storage logs.");

            return self::SUCCESS;
        }

        $selectedSheetNames = $this->selectSheets($sheetNames, $explicitSheetNames, $firstSheetsToPrint);

        if ($selectedSheetNames === []) {
            $this->warn('No sheets selected to print.');

            return self::SUCCESS;
        }

        foreach ($selectedSheetNames as $name) {
            $sheet = $spreadsheet->getSheetByName($name);

            if (! $sheet instanceof Worksheet) {
                $this->warn("Sheet not found (skipped): {$name}");

                continue;
            }

            $this->printSheetPreview($sheet, $rowsToPrint);
        }

        return self::SUCCESS;
    }

    /**
     * @param  array<int, string>  $allSheetNames
     * @param  array<int, string>  $explicitSheetNames
     * @return array<int, string>
     */
    private function selectSheets(array $allSheetNames, array $explicitSheetNames, int $firstSheetsToPrint): array
    {
        if ($explicitSheetNames !== []) {
            $missing = array_values(array_diff($explicitSheetNames, $allSheetNames));
            foreach ($missing as $name) {
                $this->warn("Requested sheet not found: {$name}");
            }

            return array_values(array_intersect($explicitSheetNames, $allSheetNames));
        }

        if ($firstSheetsToPrint <= 0) {
            return [];
        }

        return array_slice($allSheetNames, 0, $firstSheetsToPrint);
    }

    private function printSheetPreview(Worksheet $sheet, int $rowsToPrint): void
    {
        $sheetName = $sheet->getTitle();
        $highestRow = (int) $sheet->getHighestDataRow();
        $highestColumn = (string) $sheet->getHighestDataColumn();

        $this->info("Sheet: {$sheetName}");
        $this->line("Size: rows={$highestRow}, maxCol={$highestColumn}");

        if ($rowsToPrint <= 0) {
            $this->newLine();

            return;
        }

        $rows = $sheet->rangeToArray(
            "A1:{$highestColumn}".min($rowsToPrint, max(1, $highestRow)),
            null,
            true,
            true,
            true
        );

        if ($rows === []) {
            $this->warn('No data.');
            $this->newLine();

            return;
        }

        $headers = $this->extractHeadersFromRows($rows);
        $tableRows = [];

        foreach ($rows as $row) {
            $tableRow = [];
            foreach ($headers as $column) {
                $tableRow[] = $row[$column] ?? null;
            }
            $tableRows[] = $tableRow;
        }

        $this->table($headers, $tableRows);
        $this->newLine();
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     * @return array<int, string>
     */
    private function extractHeadersFromRows(array $rows): array
    {
        $columns = [];

        foreach ($rows as $row) {
            foreach (array_keys($row) as $column) {
                $columns[$column] = true;
            }
        }

        $columns = array_keys($columns);
        sort($columns);

        return $columns;
    }

    private function logSheetPreview(string $relativePath, Worksheet $sheet, int $rowsToLog): void
    {
        $sheetName = $sheet->getTitle();
        $highestRow = (int) $sheet->getHighestDataRow();
        $highestColumn = (string) $sheet->getHighestDataColumn();

        $rows = $sheet->rangeToArray(
            "A1:{$highestColumn}".min($rowsToLog, max(1, $highestRow)),
            null,
            true,
            true,
            true
        );

        Log::info('share.xlsx sheet preview', [
            'path' => $relativePath,
            'sheet' => $sheetName,
            'highest_row' => $highestRow,
            'highest_column' => $highestColumn,
            'rows_logged' => min($rowsToLog, max(1, $highestRow)),
            'rows' => $rows,
        ]);
    }
}
