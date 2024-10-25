<?php

namespace App\Providers;

use App\Repository\BookingRepository;
use App\Repository\CategoryRepository;
use App\Repository\Contracts\BookingRepositoryInterface;
use App\Repository\Contracts\CategoryRepositoryInterface;
use App\Repository\Contracts\WorkshopRepositoryInterface;
use App\Repository\WorkshopRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(CategoryRepositoryInterface::class, CategoryRepository::class);
        $this->app->singleton(WorkshopRepositoryInterface::class, WorkshopRepository::class);
        $this->app->singleton(BookingRepositoryInterface::class, BookingRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
