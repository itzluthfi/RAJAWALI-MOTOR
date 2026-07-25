<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Sales;
use Illuminate\Database\Seeder;

class SalesSeeder extends Seeder
{
    public function run(): void
    {
        $daftar = [
            ['nama' => 'Dedi Kurniawan', 'telepon' => '0812-1111-2222', 'persentase_komisi' => 2.0],
            ['nama' => 'Rina Anggraini', 'telepon' => '0813-3333-4444', 'persentase_komisi' => 2.5],
        ];

        foreach ($daftar as $data) {
            Sales::updateOrCreate(['nama' => $data['nama']], $data);
        }
    }
}
