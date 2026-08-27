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
