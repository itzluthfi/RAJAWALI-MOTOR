<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'plat_nomor')) {
                $table->string('plat_nomor')->nullable()->after('nama');
            }
            if (!Schema::hasColumn('customers', 'no_wa')) {
                $table->string('no_wa')->nullable()->after('telepon');
            }
            if (!Schema::hasColumn('customers', 'jenis_kendaraan')) {
                $table->string('jenis_kendaraan')->nullable()->after('plat_nomor');
            }
            if (!Schema::hasColumn('customers', 'kategori')) {
                $table->string('kategori', 20)->default('umum')->after('jenis_kendaraan'); // umum, mitra, grosir
            }
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['plat_nomor', 'no_wa', 'jenis_kendaraan', 'kategori']);
        });
    }
};
