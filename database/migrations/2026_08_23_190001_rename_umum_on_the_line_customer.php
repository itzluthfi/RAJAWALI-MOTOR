<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('customers')
            ->where('nama', 'like', '%On The Line%')
            ->update(['nama' => 'Umum']);
    }

    public function down(): void
    {
        // No-op
    }
};
