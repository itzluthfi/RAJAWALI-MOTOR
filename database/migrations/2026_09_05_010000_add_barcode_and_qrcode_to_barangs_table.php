<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('barangs', function (Blueprint $table) {
            if (!Schema::hasColumn('barangs', 'barcode')) {
                $table->string('barcode', 100)->nullable()->after('nama')->index();
            }
            if (!Schema::hasColumn('barangs', 'qrcode')) {
                $table->string('qrcode', 100)->nullable()->after('barcode')->index();
            }
        });

        if (Schema::hasTable('barang_barcodes')) {
            $barcodes = DB::table('barang_barcodes')->get();
            foreach ($barcodes as $bc) {
                DB::table('barangs')->where('id', $bc->barang_id)->whereNull('barcode')->update(['barcode' => $bc->barcode]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('barangs', function (Blueprint $table) {
            if (Schema::hasColumn('barangs', 'qrcode')) {
                $table->dropColumn('qrcode');
            }
            if (Schema::hasColumn('barangs', 'barcode')) {
                $table->dropColumn('barcode');
            }
        });
    }
};