<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stok_mutasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_id')->constrained();
            $table->date('tanggal');
            $table->enum('jenis_mutasi', [
                'pembelian', 'penjualan', 'retur_penjualan', 'retur_pembelian', 'penyesuaian', 'service',
            ]);
            $table->string('no_dokumen');
            $table->decimal('masuk', 15, 3)->default(0);
            $table->decimal('keluar', 15, 3)->default(0);
            $table->decimal('hpp', 18, 2)->default(0);
            $table->string('keterangan')->nullable();
            $table->timestamps();

            $table->index(['barang_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stok_mutasis');
    }
};
