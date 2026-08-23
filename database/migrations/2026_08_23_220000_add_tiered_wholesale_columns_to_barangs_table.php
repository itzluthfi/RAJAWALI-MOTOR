<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('barangs', function (Blueprint $table) {
            $table->decimal('min_qty_grosir_1', 15, 3)->default(3)->after('harga_grosir');
            $table->decimal('harga_grosir_1', 18, 2)->default(0)->after('min_qty_grosir_1');
            $table->decimal('min_qty_grosir_2', 15, 3)->default(24)->after('harga_grosir_1');
            $table->decimal('harga_grosir_2', 18, 2)->default(0)->after('min_qty_grosir_2');
        });
    }

    public function down(): void
    {
        Schema::table('barangs', function (Blueprint $table) {
            $table->dropColumn(['min_qty_grosir_1', 'harga_grosir_1', 'min_qty_grosir_2', 'harga_grosir_2']);
        });
    }
};
