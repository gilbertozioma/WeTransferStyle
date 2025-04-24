<?php

namespace App\Console\Commands;

use App\Services\UploadService;
use Illuminate\Console\Command;

class CleanExpiredUploads extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clean:expired-uploads';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete expired upload files and records';

    /**
     * Execute the console command.
     */
    public function handle(UploadService $uploadService): int
    {
        $this->info('Starting cleanup of expired uploads...');

        $count = $uploadService->cleanExpiredUploads();

        $this->info("Successfully deleted {$count} expired upload sessions.");

        return Command::SUCCESS;
    }
}
