<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Catatan: TIDAK ada kolom stok di sini secara sengaja.
        // Stok wajib berupa agregasi tabel stok_mutasis (lihat StokService).
        Schema::create('barangs', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->string('nama');
            $table->foreignId('group_id')->constrained();
            $table->foreignId('sub_group_id')->nullable()->constrained();
            $table->foreignId('satuan_id')->constrained();
            $table->decimal('harga_beli_terakhir', 18, 2)->default(0);
            $table->decimal('hpp', 18, 2)->default(0);
            $table->decimal('harga_eceran', 18, 2)->default(0);
            $table->decimal('harga_grosir', 18, 2)->default(0);
            $table->decimal('stok_minimum', 15, 3)->default(0);
            $table->string('lokasi_rak')->nullable();
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barangs');
    }
};
