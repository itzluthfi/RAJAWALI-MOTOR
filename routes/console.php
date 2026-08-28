<?php

use App\Services\DatabaseBackupService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('app:backup-database', function () {
    $this->info('Starting database backup for Rajawali Motor...');
    $filename = DatabaseBackupService::createBackup();
    $this->info("Backup created successfully: {$filename}");

    $pruned = DatabaseBackupService::pruneOldBackups(14);
    if ($pruned > 0) {
        $this->info("Pruned {$pruned} old backup file(s).");
    }
})->purpose('Backup the database to a SQL file');

Schedule::command('app:backup-database')->dailyAt('23:59');

Artisan::command('app:clean-jasa-from-barang', function () {
    $jasaBarangs = \App\Models\Barang::where('kode', 'LIKE', 'JSA-%')
        ->orWhereHas('group', fn($g) => $g->where('nama', 'LIKE', '%Jasa%'))
        ->get();
    $count = $jasaBarangs->count();
    foreach ($jasaBarangs as $b) {
        \App\Models\StokMutasi::where('barang_id', $b->id)->delete();
        \App\Models\BarangBarcode::where('barang_id', $b->id)->delete();
        $b->delete();
    }
    $this->info("Successfully cleaned {$count} dummy jasa items from barangs table.");
});
