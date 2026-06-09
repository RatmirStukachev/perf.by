<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\ZoomosImportService;

class ImportZoomosMissingImages extends Command
{
    protected $signature = 'zoomos:import-missing-images';

    protected $description = 'Download images from Zoomos for products without image';

    public function __construct(private ZoomosImportService $importService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        \DB::connection()->getPdo()->setAttribute(\PDO::ATTR_EMULATE_PREPARES, true);

        $apiKey = config('services.zoomos.api_key');

        if (empty($apiKey)) {
            $this->error('Zoomos API key is not configured. Please set ZOOMOS_API_KEY in your .env file.');

            return self::FAILURE;
        }

        $this->info('Starting Zoomos missing images import...');
        Log::info('Starting Zoomos missing images import...');
        $this->newLine();

        $progressBar = null;

        $stats = $this->importService->downloadMissingImages($apiKey, function ($processed, $total) use (&$progressBar) {
            if (! $progressBar) {
                $progressBar = $this->output->createProgressBar($total);
                $progressBar->setFormat('verbose');
            }

            $progressBar->setProgress($processed);
        });

        if ($progressBar) {
            $progressBar->finish();
            $this->newLine(2);
        }

        Log::info('Zoomos missing images import stats', $stats);
        $this->info('Missing images import completed!');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Downloaded', $stats['downloaded']],
                ['Skipped', $stats['skipped']],
                ['Total processed', $stats['total']],
            ]
        );

        return self::SUCCESS;
    }
}
