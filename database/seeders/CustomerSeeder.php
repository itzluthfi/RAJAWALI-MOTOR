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
            [
                'nama' => 'Umum / On The Line',
                'plat_nomor' => null,
                'jenis_kendaraan' => null,
                'kategori' => 'umum',
                'telepon' => null,
                'no_wa' => null,
                'alamat' => null,
                'termin_hari' => 0
            ],
            [
                'nama' => 'Andi Wijaya (Mitra)',
                'plat_nomor' => 'L 5432 AB',
                'jenis_kendaraan' => 'Honda Vario 150',
                'kategori' => 'mitra',
                'telepon' => '081234567890',
                'no_wa' => '081234567890',
                'alamat' => 'Jl. Pahlawan No. 45, Sidoarjo',
                'termin_hari' => 30
            ],
            [
                'nama' => 'Bengkel Sumber Rejeki (Grosir)',
                'plat_nomor' => 'W 8901 CD',
                'jenis_kendaraan' => 'Yamaha NMAX 155',
                'kategori' => 'grosir',
                'telepon' => '081399881122',
                'no_wa' => '081399881122',
                'alamat' => 'Ruko Jasem No. 12, Sidoarjo',
                'termin_hari' => 30
            ],
            [
                'nama' => 'Budi Santoso',
                'plat_nomor' => 'W 1234 EF',
                'jenis_kendaraan' => 'Honda Beat FI',
                'kategori' => 'umum',
                'telepon' => '085648888441',
                'no_wa' => '085648888441',
                'alamat' => 'Jl. Samanhudi No. 10, Sidoarjo',
                'termin_hari' => 30
            ],
        ];

        foreach ($daftar as $data) {
            Customer::updateOrCreate(['nama' => $data['nama']], $data);
        }
    }
}
