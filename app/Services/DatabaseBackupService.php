<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class DatabaseBackupService
{
    public static function createBackup(): string
    {
        $backupDir = storage_path('app/backups');
        if (!File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        $filename = 'backup_rajawalimotor_' . date('Y-m-d_His') . '.sql';
        $filepath = $backupDir . '/' . $filename;

        $driver = DB::getDriverName();
        $handle = fopen($filepath, 'w+');

        fwrite($handle, "-- ========================================================\n");
        fwrite($handle, "-- RAJAWALI MOTOR DATABASE BACKUP\n");
        fwrite($handle, "-- Generated At: " . date('Y-m-d H:i:s') . "\n");
        fwrite($handle, "-- Driver: {$driver}\n");
        fwrite($handle, "-- ========================================================\n\n");

        if ($driver === 'sqlite') {
            $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
            foreach ($tables as $tObj) {
                $table = $tObj->name;
                $createTable = DB::select("SELECT sql FROM sqlite_master WHERE type='table' AND name = ?", [$table]);
                $createSql = $createTable[0]->sql ?? '';

                fwrite($handle, "-- Table structure for `{$table}`\n");
                fwrite($handle, "DROP TABLE IF EXISTS `{$table}`;\n");
                fwrite($handle, $createSql . ";\n\n");

                $rows = DB::table($table)->get();
                if ($rows->count() > 0) {
                    fwrite($handle, "-- Data for `{$table}`\n");
                    foreach ($rows as $row) {
                        $rowArray = (array) $row;
                        $values = array_map(function ($val) {
                            if ($val === null) {
                                return 'NULL';
                            }
                            return "'" . addslashes((string) $val) . "'";
                        }, $rowArray);
                        fwrite($handle, "INSERT INTO `{$table}` VALUES (" . implode(', ', $values) . ");\n");
                    }
                    fwrite($handle, "\n");
                }
            }
        } else {
            fwrite($handle, "SET FOREIGN_KEY_CHECKS=0;\n\n");
            $tables = DB::select('SHOW TABLES');
            foreach ($tables as $tableObj) {
                $table = reset($tableObj);
                $createTable = DB::select("SHOW CREATE TABLE `{$table}`");
                $createSql = $createTable[0]->{'Create Table'} ?? '';

                fwrite($handle, "-- Table structure for table `{$table}`\n");
                fwrite($handle, "DROP TABLE IF EXISTS `{$table}`;\n");
                fwrite($handle, $createSql . ";\n\n");

                $rows = DB::table($table)->get();
                if ($rows->count() > 0) {
                    fwrite($handle, "-- Dumping data for table `{$table}` (" . $rows->count() . " rows)\n");
                    foreach ($rows as $row) {
                        $rowArray = (array) $row;
                        $values = array_map(function ($val) {
                            if ($val === null) {
                                return 'NULL';
                            }
                            return "'" . addslashes((string) $val) . "'";
                        }, $rowArray);
                        fwrite($handle, "INSERT INTO `{$table}` VALUES (" . implode(', ', $values) . ");\n");
                    }
                    fwrite($handle, "\n");
                }
            }
            fwrite($handle, "SET FOREIGN_KEY_CHECKS=1;\n");
        }

        fclose($handle);
        return $filename;
    }

    public static function getBackupList(): array
    {
        $backupDir = storage_path('app/backups');
        if (!File::exists($backupDir)) {
            return [];
        }

        $files = File::files($backupDir);
        $backups = [];

        foreach ($files as $file) {
            if ($file->getExtension() === 'sql' || $file->getExtension() === 'gz') {
                $backups[] = [
                    'filename' => $file->getFilename(),
                    'path' => $file->getPathname(),
                    'size' => self::formatBytes($file->getSize()),
                    'raw_size' => $file->getSize(),
                    'created_at' => date('d M Y H:i', $file->getMTime()),
                    'timestamp' => $file->getMTime(),
                ];
            }
        }

        // Sort latest first
        usort($backups, fn ($a, $b) => $b['timestamp'] <=> $a['timestamp']);

        return $backups;
    }

    public static function pruneOldBackups(int $keepDays = 14): int
    {
        $backupDir = storage_path('app/backups');
        if (!File::exists($backupDir)) {
            return 0;
        }

        $deleted = 0;
        $cutoff = time() - ($keepDays * 86400);

        foreach (File::files($backupDir) as $file) {
            if ($file->getMTime() < $cutoff) {
                File::delete($file->getPathname());
                $deleted++;
            }
        }

        return $deleted;
    }

    private static function formatBytes(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' B';
    }
}
