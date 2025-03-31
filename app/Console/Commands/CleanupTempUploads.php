<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanupTempUploads extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cleanup:temp-uploads';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove old files from temporary uploads directory';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $files = Storage::disk('temp_uploads')->allFiles();

        foreach ($files as $file) {
            if (Storage::disk('temp_uploads')->lastModified($file) < now()->subHours(24)->getTimestamp()) {
                Storage::disk('temp_uploads')->delete($file);
            }
        }

        $this->info('Old temporary files deleted.');
    }
}
