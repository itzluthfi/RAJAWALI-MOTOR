<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel singleton: hanya berisi 1 baris pengaturan aktif.
        Schema::create('pengaturan_tokos', function (Blueprint $table) {
            $table->id();
            $table->string('nama_toko');
            $table->string('alamat')->nullable();
            $table->string('telepon')->nullable();
            $table->string('format_nota')->default('PJ{tahun}{urutan}');
            $table->decimal('batas_diskon_kasir_persen', 5, 2)->default(0);
            $table->boolean('izinkan_stok_minus')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengaturan_tokos');
    }
};
