<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Imports\PromotionImport;
use App\Models\Promotion;
use Maatwebsite\Excel\Facades\Excel;
use Exception;

class ImportPromotionsCommand extends Command
{
    protected $signature = 'promotions:import
                           {file : Path to Excel/CSV file}
                           {--update-existing : Update existing promotions}
                           {--preview : Preview the file without importing}';

    protected $description = 'Import promotions from Excel/CSV';
    public function handle()
    {
        $filePath = $this->argument('file');
        $updateExisting = $this->option('update-existing');
        $preview = $this->option('preview');

        if (!file_exists($filePath)) {
            $this->error("File not found: {$filePath}");
            return 1;
        }

        // Validate file extension
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        if (!in_array($extension, ['csv', 'xlsx', 'xls'])) {
            $this->error("Unsupported file format. Please use CSV, XLSX, or XLS files.");
            return 1;
        }

        $this->info("Processing file: {$filePath}");
        $this->info("File type: " . strtoupper($extension));

        try {
            if ($preview) {
                return $this->previewFile($filePath);
            }

            return $this->importFile($filePath, $updateExisting);

        } catch (Exception $e) {
            $this->error("Import failed: {$e->getMessage()}");

            // Show more detailed error for debugging
            if ($this->option('verbose')) {
                $this->line($e->getTraceAsString());
            }

            return 1;
        }
    }

    /**
     * Preview file contents without importing
     */
    protected function previewFile(string $filePath): int
    {
        $this->info("Previewing file structure...");

        try {
            // Read first few rows
            $preview = Excel::toArray(new class {
                use \Maatwebsite\Excel\Concerns\Importable;
            }, $filePath);

            $rows = $preview[0] ?? [];

            if (empty($rows)) {
                $this->error("File appears to be empty or unreadable");
                return 1;
            }

            $header = array_shift($rows);
            $this->info("Found " . count($rows) . " data rows");

            // Show header structure
            $this->line("\nFile Structure:");
            $this->table(['Column Index', 'Header'], collect($header)->map(function ($value, $index) {
                return [$index, $value ?: '(empty)'];
            })->toArray());

            // Show sample data
            if (!empty($rows)) {
                $this->line("\nSample Data (first 3 rows):");
                $sampleRows = array_slice($rows, 0, 3);

                foreach ($sampleRows as $index => $row) {
                    $this->line("Row " . ($index + 2) . ":");
                    $this->table(['Column', 'Value'], collect($row)->map(function ($value, $colIndex) use ($header) {
                        $headerName = $header[$colIndex] ?? "Column {$colIndex}";
                        return [$headerName, $value ?: '(empty)'];
                    })->toArray());
                    $this->line("");
                }
            }

            // Validation preview
            $this->line("Column Mapping:");
            $expectedColumns = [
                0 => 'Location Code',
                1 => 'Location Name',
                6 => 'Stock Code',
                9 => 'Date From',
                10 => 'Date To',
                11 => 'Selling Price 1',
                12 => 'Selling Price 2',
                13 => 'Selling Price 3',
                14 => 'Selling Price 4'
            ];

            foreach ($expectedColumns as $index => $expected) {
                $actual = $header[$index] ?? '(missing)';
                $status = !empty($header[$index]) ? '✓' : '✗';
                $this->line("  {$status} Column {$index}: Expected '{$expected}', Found '{$actual}'");
            }

            return 0;

        } catch (Exception $e) {
            $this->error("Failed to preview file: {$e->getMessage()}");
            return 1;
        }
    }

    /**
     * Import the file
     */
    protected function importFile(string $filePath, bool $updateExisting): int
    {
        $this->info("Starting import...");

        if ($updateExisting) {
            $this->warn("Update mode: Existing promotions will be updated");
        } else {
            $this->info("Create mode: Existing promotions will be skipped");
        }

        // Create import instance
        $import = new PromotionImport($updateExisting);

        // Show progress bar
        $this->withProgressBar(1, function () use ($import, $filePath) {
            Excel::import($import, $filePath);
        });

        $this->newLine(2);

        // Get results
        $result = $import->getResults();

        // Display results
        $this->info("Import completed!");
        $this->table(['Metric', 'Count'], [
            ['Total Rows Processed', $result['processed_rows']],
            ['Successful Imports', $result['successful_rows']],
            ['Errors', $result['error_count']],
            ['Warnings', $result['warning_count']],
            ['Batch ID', $result['batch_id']]
        ]);

        // Show detailed results
        if (!empty($result['imported'])) {
            $this->line("\nSuccessfully Imported:");
            foreach (array_slice($result['imported'], 0, 10) as $item) {
                $this->line("  ✓ {$item}");
            }
            if (count($result['imported']) > 10) {
                $this->line("  ... and " . (count($result['imported']) - 10) . " more");
            }
        }

        if (!empty($result['errors'])) {
            $this->line("\nErrors:");
            foreach ($result['errors'] as $error) {
                $this->line("  ✗ {$error}");
            }
        }

        if (!empty($result['warnings'])) {
            $this->line("\nWarnings:");
            foreach ($result['warnings'] as $warning) {
                $this->line("  ⚠ {$warning}");
            }
        }

        // Summary
        if ($result['successful_rows'] > 0) {
            $this->info("\n🎉 Import successful! {$result['successful_rows']} promotions imported.");

            if ($result['error_count'] > 0) {
                $this->warn("Note: {$result['error_count']} rows had errors and were skipped.");
            }

            return 0;
        } else {
            $this->error("\n❌ Import failed: No rows were successfully processed.");
            return 1;
        }
    }
}

class CleanupExpiredPromotionsCommand extends Command
{
    protected $signature = 'promotions:cleanup
                           {--days=30 : Number of days after expiration to keep promotions}
                           {--dry-run : Show what would be deleted without actually deleting}
                           {--batch=100 : Number of records to process in each batch}';

    protected $description = 'Cleanup expired promotions and old import data';

    public function handle()
    {
        $days = $this->option('days');
        $dryRun = $this->option('dry-run');
        $batchSize = $this->option('batch');

        $cutoffDate = now()->subDays($days);

        $this->info("Cleaning up promotions expired more than {$days} days ago (before {$cutoffDate->format('Y-m-d')})");

        $query = Promotion::where('ends_at', '<', $cutoffDate)
            ->where('status', 'expired');

        $count = $query->count();

        if ($count === 0) {
            $this->info("No expired promotions found to cleanup.");
            return 0;
        }

        if ($dryRun) {
            $this->info("DRY RUN: Would delete {$count} expired promotions");

            $this->line("\nPromotions that would be deleted:");
            $promotions = $query->take(10)->get(['id', 'name', 'ends_at', 'stock_code']);

            $this->table(['ID', 'Name', 'Stock Code', 'Expired Date'],
                $promotions->map(function ($promotion) {
                    return [
                        $promotion->id,
                        Str::limit($promotion->name, 40),
                        $promotion->stock_code,
                        $promotion->ends_at->format('Y-m-d')
                    ];
                })->toArray()
            );

            if ($count > 10) {
                $this->line("... and " . ($count - 10) . " more");
            }

            return 0;
        }

        if (!$this->confirm("Delete {$count} expired promotions?")) {
            $this->info("Cleanup cancelled");
            return 0;
        }

        // Delete in batches to avoid memory issues
        $deleted = 0;
        $this->withProgressBar($count, function () use ($query, $batchSize, &$deleted) {
            do {
                $batch = $query->take($batchSize)->get();
                if ($batch->isEmpty()) {
                    break;
                }

                $batchIds = $batch->pluck('id');
                $batchDeleted = Promotion::whereIn('id', $batchIds)->delete();
                $deleted += $batchDeleted;

                $this->advance($batchDeleted);

            } while ($batch->count() === $batchSize);
        });

        $this->newLine();
        $this->info("Successfully deleted {$deleted} expired promotions");

        return 0;
    }
}

class UpdatePromotionStatusCommand extends Command
{
    protected $signature = 'promotions:update-status
                           {--batch=500 : Number of records to process in each batch}';

    protected $description = 'Update promotion statuses based on current date and update featured products';

    public function handle()
    {
        $batchSize = $this->option('batch');
        $now = now();

        $this->info("Updating promotion statuses...");

        // Mark expired promotions
        $expiredCount = Promotion::where('ends_at', '<', $now)
            ->where('status', '!=', 'expired')
            ->update(['status' => 'expired']);

        // Mark scheduled promotions as active
        $activatedCount = Promotion::where('starts_at', '<=', $now)
            ->where('ends_at', '>', $now)
            ->where('status', 'scheduled')
            ->update(['status' => 'active']);

        // Update product featured status in batches
        $featuredCount = 0;
        Promotion::active()
            ->chunk($batchSize, function ($promotions) use (&$featuredCount) {
                $stockCodes = $promotions->pluck('stock_code')->unique();

                // Set featured status for promoted products
                \DB::table('products')
                    ->whereIn('StockCode', $stockCodes)
                    ->update(['is_featured' => 1]);

                $featuredCount += $stockCodes->count();
            });

        // Clear featured status for products without active promotions
        $clearedCount = \DB::table('products')
            ->whereNotIn('StockCode', function ($query) {
                $query->select('stock_code')
                    ->from('promotions')
                    ->where('status', 'active')
                    ->where('starts_at', '<=', now())
                    ->where('ends_at', '>', now());
            })
            ->where('is_featured', 1)
            ->update(['is_featured' => 0]);

        // Display results
        $this->table(['Action', 'Count'], [
            ['Promotions Expired', $expiredCount],
            ['Promotions Activated', $activatedCount],
            ['Products Set as Featured', $featuredCount],
            ['Products Unfeatured', $clearedCount]
        ]);

        $totalChanges = $expiredCount + $activatedCount + $clearedCount;

        if ($totalChanges > 0) {
            $this->info("✅ Status update completed: {$totalChanges} changes made");
        } else {
            $this->info("ℹ️  No status changes needed");
        }

        return 0;
    }
}

