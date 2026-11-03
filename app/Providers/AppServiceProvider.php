<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use App\Models\StockOpname;
use App\Models\StockTransfer;
use App\Observers\StockOpnameObserver;
use App\Observers\StockTransferObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        // Notifikasi alur transfer stok & opname terpusat (API + web admin + web super admin)
        StockTransfer::observe(StockTransferObserver::class);
        StockOpname::observe(StockOpnameObserver::class);
    }
}