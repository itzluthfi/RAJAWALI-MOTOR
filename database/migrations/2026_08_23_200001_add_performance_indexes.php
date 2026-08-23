<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penjualans', function (Blueprint $table) {
            $table->index(['created_at', 'status_bayar'], 'idx_penjualans_created_status');
            $table->index('metode_pembayaran', 'idx_penjualans_metode');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->index('plat_nomor', 'idx_customers_plat');
            $table->index('no_wa', 'idx_customers_wa');
            $table->index('kategori', 'idx_customers_kategori');
        });

        Schema::table('kas_flows', function (Blueprint $table) {
            $table->index(['tanggal', 'sumber', 'tipe'], 'idx_kas_flows_tanggal_sumber_tipe');
        });
    }

    public function down(): void
    {
        Schema::table('penjualans', function (Blueprint $table) {
            $table->dropIndex('idx_penjualans_created_status');
            $table->dropIndex('idx_penjualans_metode');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropIndex('idx_customers_plat');
            $table->dropIndex('idx_customers_wa');
            $table->dropIndex('idx_customers_kategori');
        });

        Schema::table('kas_flows', function (Blueprint $table) {
            $table->dropIndex('idx_kas_flows_tanggal_sumber_tipe');
        });
    }
};
