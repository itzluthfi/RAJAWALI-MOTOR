<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $daftar = [
            ['nama' => 'Umum / On The Line', 'telepon' => null, 'alamat' => null, 'termin_hari' => 0],
            ['nama' => 'Toko Jaya Motor', 'telepon' => '0812-3456-7890', 'alamat' => null, 'termin_hari' => 14],
            ['nama' => 'Bengkel Sumber Rejeki', 'telepon' => '0813-9988-1122', 'alamat' => null, 'termin_hari' => 30],
        ];

        foreach ($daftar as $data) {
            Customer::updateOrCreate(['nama' => $data['nama']], $data);
        }
    }
}
