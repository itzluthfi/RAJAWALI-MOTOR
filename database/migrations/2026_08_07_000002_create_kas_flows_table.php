<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('kas_flows', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('tipe', 20); // masuk, keluar
            $table->string('sumber', 20); // kas, bank
            $table->string('kategori', 30)->default('dll'); // penjualan, pembelian, operasional, hutang, piutang, dll
            $table->string('no_referensi')->nullable();
            $table->decimal('nominal', 14, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kas_flows');
    }
};
