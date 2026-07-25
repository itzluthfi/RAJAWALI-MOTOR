<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Satuan;
use Illuminate\Database\Seeder;

class SatuanSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['PCS', 'SET', 'BOTOL', 'LITER'] as $nama) {
            Satuan::updateOrCreate(['nama' => $nama]);
        }
    }
}
