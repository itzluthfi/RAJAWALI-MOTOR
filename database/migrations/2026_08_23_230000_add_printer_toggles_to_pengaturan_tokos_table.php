<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengaturan_tokos', function (Blueprint $table) {
            $table->boolean('printer_struk_aktif')->default(false)->after('izinkan_stok_minus');
            $table->boolean('printer_faktur_aktif')->default(false)->after('printer_struk_aktif');
        });
    }

    public function down(): void
    {
        Schema::table('pengaturan_tokos', function (Blueprint $table) {
            $table->dropColumn(['printer_struk_aktif', 'printer_faktur_aktif']);
        });
    }
};
