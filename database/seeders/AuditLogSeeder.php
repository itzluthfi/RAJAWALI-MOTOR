<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Seeder;

class AuditLogSeeder extends Seeder
{
    public function run(): void
    {
        $userOwner = User::first();

        AuditLog::create([
            'user_id' => $userOwner?->id,
            'nama_user' => $userOwner?->name ?? 'Budi Santoso',
            'aksi' => 'Simpan Transaksi Kasir',
            'modul' => 'Kasir POS',
            'objek' => 'PJ2026080001',
            'ip_address' => '127.0.0.1',
            'perubahan' => 'Total: Rp 125.000 (Tunai)',
        ]);

        AuditLog::create([
            'user_id' => $userOwner?->id,
            'nama_user' => $userOwner?->name ?? 'Budi Santoso',
            'aksi' => 'Tambah Service Bengkel',
            'modul' => 'Service',
            'objek' => 'SV2026080001',
            'ip_address' => '127.0.0.1',
            'perubahan' => 'Plat: L 5432 AB - Honda Vario 150',
        ]);

        AuditLog::create([
            'user_id' => $userOwner?->id,
            'nama_user' => $userOwner?->name ?? 'Budi Santoso',
            'aksi' => 'Update Pengaturan Toko',
            'modul' => 'Pengaturan',
            'objek' => 'Rajawali Motor',
            'ip_address' => '127.0.0.1',
            'perubahan' => 'Alamat diperbarui ke Jl. Samanhudi No.102, Sidoarjo',
        ]);
    }
}
