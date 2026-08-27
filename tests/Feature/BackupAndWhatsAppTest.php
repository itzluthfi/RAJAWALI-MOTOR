<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\Customer;
use App\Models\Group;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use App\Models\Satuan;
use App\Models\User;
use App\Services\DatabaseBackupService;
use App\Services\WhatsAppReceiptService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class BackupAndWhatsAppTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_backup_service_creates_file(): void
    {
        $filename = DatabaseBackupService::createBackup();
        $this->assertNotEmpty($filename);
        $this->assertFileExists(storage_path('app/backups/' . $filename));

        $backups = DatabaseBackupService::getBackupList();
        $this->assertNotEmpty($backups);
        $this->assertEquals($filename, $backups[0]['filename']);

        // Clean up
        File::delete(storage_path('app/backups/' . $filename));
    }

    public function test_whatsapp_receipt_service_generates_correct_text(): void
    {
        $group = Group::create(['nama' => 'Oli & Pelumas']);
        $satuan = Satuan::create(['nama' => 'Botol']);
        $user = User::create([
            'name' => 'Kasir Toko',
            'username' => 'kasir_test',
            'email' => 'kasir@rajawali.com',
            'password' => bcrypt('password'),
            'peran' => 'kasir',
            'aktif' => true,
        ]);

        $customer = Customer::create([
            'nama' => 'Budi Santoso',
            'telepon' => '081234567890',
            'plat_nomor' => 'W 1234 XY',
            'jenis_kendaraan' => 'Vario 125',
        ]);

        $barang = Barang::create([
            'kode' => 'BRG001',
            'nama' => 'Oli MPX2 0.8L',
            'group_id' => $group->id,
            'satuan_id' => $satuan->id,
            'hpp' => 45000,
            'harga_eceran' => 55000,
            'stok_minimum' => 5,
        ]);

        $penjualan = Penjualan::create([
            'nomor_nota' => 'PJ2026080099',
            'user_id' => $user->id,
            'customer_id' => $customer->id,
            'subtotal' => 55000,
            'diskon' => 0,
            'total_akhir' => 55000,
            'metode_pembayaran' => 'tunai',
            'status_bayar' => 'lunas',
        ]);

        PenjualanDetail::create([
            'penjualan_id' => $penjualan->id,
            'barang_id' => $barang->id,
            'qty' => 1,
            'harga_satuan' => 55000,
            'hpp' => 45000,
            'diskon' => 0,
            'subtotal' => 55000,
        ]);

        $text = WhatsAppReceiptService::buatTeksNota($penjualan);
        $this->assertStringContainsString('RAJAWALI MOTOR', $text);
        $this->assertStringContainsString('PJ2026080099', $text);
        $this->assertStringContainsString('Budi Santoso', $text);
        $this->assertStringContainsString('Oli MPX2 0.8L', $text);
        $this->assertStringContainsString('55.000', $text);

        $url = WhatsAppReceiptService::buatUrlWhatsApp($customer->telepon, $text);
        $this->assertStringStartsWith('https://wa.me/6281234567890', $url);
    }
}
