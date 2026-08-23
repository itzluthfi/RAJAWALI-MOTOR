<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Hapus username lama (kasir1, gudang1, montir1) agar tidak bentrok
        User::whereIn('username', ['kasir1', 'gudang1', 'montir1'])->delete();

        $akun = [
            ['name' => 'Budi Santoso', 'username' => 'owner', 'peran' => 'owner'],
            ['name' => 'Admin Toko', 'username' => 'admin', 'peran' => 'admin'],
            ['name' => 'Sari Wulandari', 'username' => 'kasir', 'peran' => 'kasir'],
        ];

        foreach ($akun as $data) {
            User::updateOrCreate(
                ['username' => $data['username']],
                [
                    'name' => $data['name'],
                    'email' => $data['username'].'@rajawalimotor.test',
                    'password' => 'password',
                    'peran' => $data['peran'],
                    'aktif' => true,
                ]
            );
        }
    }
}
