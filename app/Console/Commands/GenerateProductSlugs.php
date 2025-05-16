<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateProductSlugs extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:generate-slugs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate slugs for existing products';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Generating slugs for existing products...');

        $productsWithoutSlug = Product::whereNull('slug')->orWhere('slug', '')->get();
        $count = $productsWithoutSlug->count();

        $this->info("Found {$count} products without slugs.");

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        foreach ($productsWithoutSlug as $product) {
            $product->slug = Str::slug($product->StockItemName);
            $product->save();
            $bar->advance();
        }

        $bar->finish();
        $this->newline();
        $this->info('Slugs generated successfully.');

        return Command::SUCCESS;
    }
}
