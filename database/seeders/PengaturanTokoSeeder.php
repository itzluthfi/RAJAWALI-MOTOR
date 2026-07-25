<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\PengaturanToko;
use Illuminate\Database\Seeder;

class PengaturanTokoSeeder extends Seeder
{
    public function run(): void
    {
        PengaturanToko::current();
    }
}
