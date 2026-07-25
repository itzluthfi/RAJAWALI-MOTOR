<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $daftar = [
            ['nama' => 'PT Astra Otoparts', 'telepon' => '031-8877665', 'alamat' => null],
            ['nama' => 'CV Sinar Motor Jaya', 'telepon' => '031-5566778', 'alamat' => null],
        ];

        foreach ($daftar as $data) {
            Supplier::updateOrCreate(['nama' => $data['nama']], $data);
        }
    }
}
