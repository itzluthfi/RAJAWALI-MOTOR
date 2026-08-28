<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\PengaturanToko;
use App\Models\Penjualan;
use App\Models\Service;

class WhatsAppReceiptService
{
    public static function buatTeksNota(Penjualan $penjualan): string
    {
        $pengaturan = PengaturanToko::current();

        $namaToko = "*" . strtoupper($pengaturan->nama_toko) . "*";
        $alamat = $pengaturan->alamat ?? 'Sidoarjo';
        $telepon = $pengaturan->telepon ? "WA/Telp: " . $pengaturan->telepon : '';
        $garis = "----------------------------------------";

        $pesan = "{$namaToko}\n{$alamat}\n";
        if ($telepon) {
            $pesan .= "{$telepon}\n";
        }
        $pesan .= "{$garis}\n";
        $pesan .= "No Nota: *{$penjualan->nomor_nota}*\n";
        $pesan .= "Tanggal: " . $penjualan->created_at->setTimezone('Asia/Jakarta')->format('d M Y, H:i') . " WIB\n";

        if ($penjualan->customer) {
            $pesan .= "Pelanggan: *{$penjualan->customer->nama}*\n";
            if ($penjualan->customer->plat_nomor) {
                $kendaraan = $penjualan->customer->plat_nomor;
                if ($penjualan->customer->jenis_kendaraan) {
                    $kendaraan .= " (" . $penjualan->customer->jenis_kendaraan . ")";
                }
                $pesan .= "Kendaraan: {$kendaraan}\n";
            }
        } else {
            $pesan .= "Pelanggan: *Pelanggan Umum*\n";
        }

        $pesan .= "{$garis}\n*RINCIAN PEMBELIAN:*\n";

        foreach ($penjualan->details as $d) {
            $qtyFormatted = rtrim(rtrim(number_format((float) $d->qty, 3, ',', ''), '0'), ',');
            $hargaFormatted = number_format((float) $d->harga_satuan, 0, ',', '.');
            $subtotalFormatted = number_format((float) $d->subtotal, 0, ',', '.');
            
            $namaBarang = $d->barang->nama ?? 'Item Barang';
            $pesan .= "• {$qtyFormatted}x {$namaBarang} @ Rp {$hargaFormatted} = *Rp {$subtotalFormatted}*\n";
        }

        $pesan .= "{$garis}\n";
        $pesan .= "Subtotal: Rp " . number_format((float) $penjualan->subtotal, 0, ',', '.') . "\n";

        if ($penjualan->diskon > 0) {
            $pesan .= "Diskon: -Rp " . number_format((float) $penjualan->diskon, 0, ',', '.') . "\n";
        }

        $statusBayar = strtoupper($penjualan->status_bayar);
        $metode = strtoupper($penjualan->metode_pembayaran);

        $pesan .= "*TOTAL AKHIR:* *Rp " . number_format((float) $penjualan->total_akhir, 0, ',', '.') . "*\n";
        $pesan .= "Status: *{$statusBayar}* ({$metode})\n";

        if ($penjualan->status_bayar === 'piutang') {
            $sisa = max(0, $penjualan->total_akhir - $penjualan->uang_muka);
            $pesan .= "Uang Muka (DP): Rp " . number_format((float) $penjualan->uang_muka, 0, ',', '.') . "\n";
            $pesan .= "Sisa Piutang: *Rp " . number_format((float) $sisa, 0, ',', '.') . "*\n";
        }

        $pesan .= "{$garis}\n";
        $pesan .= "Terima kasih telah berbelanja di " . $pengaturan->nama_toko . "!\n";
        $pesan .= "_" . ($pengaturan->footer_struk ?? 'Garansi resmi suku cadang & servis terpercaya.') . "_";

        return $pesan;
    }

    public static function buatTeksServis(Service $service): string
    {
        $pengaturan = PengaturanToko::current();

        $namaToko = "*" . strtoupper($pengaturan->nama_toko) . "*";
        $alamat = $pengaturan->alamat ?? 'Sidoarjo';
        $telepon = $pengaturan->telepon ? "WA/Telp: " . $pengaturan->telepon : '';
        $garis = "----------------------------------------";

        $pesan = "{$namaToko}\n{$alamat}\n";
        if ($telepon) {
            $pesan .= "{$telepon}\n";
        }
        $pesan .= "{$garis}\n";
        $pesan .= "No Servis: *{$service->nomor_dokumen}*\n";
        $pesan .= "Tanggal: " . $service->tanggal_masuk->setTimezone('Asia/Jakarta')->format('d M Y, H:i') . " WIB\n";
        $pesan .= "Pelanggan: *" . ($service->customer->nama ?? 'Pelanggan') . "*\n";
        $pesan .= "Kendaraan: *" . ($service->merk_type ?? '-') . "* (" . ($service->customer->plat_nomor ?? '-') . ")\n";
        $pesan .= "Montir: *" . ($service->montir->name ?? '-') . "*\n";
        $pesan .= "Status: *" . strtoupper($service->status) . "*\n";

        $pesan .= "{$garis}\n*RINCIAN BIAYA SERVIS:*\n";
        $pesan .= "• Biaya Jasa: Rp " . number_format((float) $service->biaya_jasa, 0, ',', '.') . "\n";
        $pesan .= "• Biaya Sparepart: Rp " . number_format((float) $service->biaya_part, 0, ',', '.') . "\n";
        
        if ($service->keluhan) {
            $pesan .= "• Keluhan: _{$service->keluhan}_\n";
        }

        $pesan .= "{$garis}\n";
        $pesan .= "*TOTAL BIAYA:* *Rp " . number_format((float) ($service->grand_total_nett ?? $service->grand_total), 0, ',', '.') . "*\n";
        $pesan .= "{$garis}\n";
        $pesan .= "Terima kasih telah mempercayakan servis motor Anda kepada " . $pengaturan->nama_toko . "!\n";
        $pesan .= "_" . ($pengaturan->footer_struk ?? 'Garansi servis terjamin & sparepart original.') . "_";

        return $pesan;
    }

    public static function buatUrlWhatsApp(string $telepon, string $teks): string
    {
        $bersih = preg_replace('/[^0-9]/', '', $telepon);

        if (str_starts_with($bersih, '0')) {
            $bersih = '62' . substr($bersih, 1);
        }

        return "https://wa.me/{$bersih}?text=" . rawurlencode($teks);
    }
}
