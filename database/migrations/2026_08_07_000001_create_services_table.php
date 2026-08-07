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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_dokumen')->unique();
            $table->date('tanggal_masuk');
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->foreignId('montir_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('sales_id')->nullable()->constrained('sales')->nullOnDelete();
            $table->string('merk_type')->nullable();
            $table->string('no_rangka')->nullable();
            $table->string('no_mesin')->nullable();
            $table->string('kelengkapan')->nullable();
            $table->text('keluhan')->nullable();
            $table->text('catatan')->nullable();
            $table->string('status', 30)->default('masuk'); // masuk, dikerjakan, selesai, diambil, lunas
            $table->string('repaired_by', 20)->default('intern'); // intern, extern
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->date('tanggal_kirim')->nullable();
            $table->date('tanggal_kembali')->nullable();
            $table->decimal('grand_total_supplier', 14, 2)->default(0);
            $table->decimal('grand_total_nett', 14, 2)->default(0);
            $table->boolean('status_lunas')->default(false);
            $table->timestamps();
        });

        Schema::create('service_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
            $table->foreignId('barang_id')->constrained('barangs')->cascadeOnDelete();
            $table->decimal('qty', 10, 3)->default(1);
            $table->decimal('harga_jual', 14, 2)->default(0);
            $table->decimal('hpp', 14, 2)->default(0);
            $table->decimal('subtotal', 14, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('service_jasas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
            $table->string('nama_jasa');
            $table->decimal('harga_supplier', 14, 2)->default(0);
            $table->decimal('harga_nett', 14, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_jasas');
        Schema::dropIfExists('service_details');
        Schema::dropIfExists('services');
    }
};
