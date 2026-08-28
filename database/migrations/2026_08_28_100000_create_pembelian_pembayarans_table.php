<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pembelians', function (Blueprint $table) {
            if (!Schema::hasColumn('pembelians', 'terbayar')) {
                $table->decimal('terbayar', 15, 2)->default(0)->after('total');
            }
            if (!Schema::hasColumn('pembelians', 'tanggal_lunas')) {
                $table->dateTime('tanggal_lunas')->nullable()->after('jatuh_tempo');
            }
        });

        Schema::create('pembelian_pembayarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pembelian_id')->constrained('pembelians')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('tanggal_bayar');
            $table->decimal('nominal', 15, 2);
            $table->string('sumber', 20)->default('kas');
            $table->string('keterangan', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembelian_pembayarans');

        Schema::table('pembelians', function (Blueprint $table) {
            if (Schema::hasColumn('pembelians', 'terbayar')) {
                $table->dropColumn('terbayar');
            }
            if (Schema::hasColumn('pembelians', 'tanggal_lunas')) {
                $table->dropColumn('tanggal_lunas');
            }
        });
    }
};
