<?php

namespace App\Providers;

use App\Helpers\PricingHelper;
use App\Services\PromotionCalculationService;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if (config('app.enable_documentation', false)) {
            $this->app->register(\BinaryTorch\LaRecipe\LaRecipeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();

        Event::listen(MessageSending::class, function ($event) {
            \Log::info('Mail Sending', ['data' => $event->data]);
        });

        PricingHelper::setPromotionService(
            $this->app->make(PromotionCalculationService::class)
        );
    }
}
