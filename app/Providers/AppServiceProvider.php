<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Barang;
use App\Models\Penjualan;
use App\Services\StokService;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
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
        Blade::component('layouts.app', 'app-layout');
        Blade::component('layouts.print', 'print-layout');

        View::composer('layouts.app', function ($view) {
            $stokMenipisCount = 0;
            $notaHariIniCount = 0;

            try {
                if (auth()->check()) {
                    $stokService = app(StokService::class);
                    $allBarang = Barang::where('aktif', true)->get(['id', 'stok_minimum']);
                    $stokMap = $stokService->stokBanyakBarang($allBarang->pluck('id'));

                    $stokMenipisCount = $allBarang->filter(function ($b) use ($stokMap) {
                        return ($stokMap[$b->id] ?? 0) <= (float) $b->stok_minimum;
                    })->count();

                    $notaHariIniCount = Penjualan::whereDate('created_at', now()->toDateString())->count();
                }
            } catch (\Throwable $e) {
                // Fail-safe default
            }

            $view->with([
                'sidebarStokMenipisCount' => $stokMenipisCount,
                'sidebarNotaHariIniCount' => $notaHariIniCount,
            ]);
        });
    }
}
