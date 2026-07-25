<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sub_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained()->cascadeOnDelete();
            $table->string('nama');
            $table->timestamps();

            $table->unique(['group_id', 'nama']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sub_groups');
    }
};
