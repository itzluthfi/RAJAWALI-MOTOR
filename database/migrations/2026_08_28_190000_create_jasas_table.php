<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jasas', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 50)->unique();
            $table->string('nama', 150);
            $table->string('kategori', 100)->nullable();
            $table->decimal('tarif', 15, 2)->default(0);
            $table->decimal('komisi_montir', 15, 2)->default(0);
            $table->string('keterangan', 255)->nullable();
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jasas');
    }
};
