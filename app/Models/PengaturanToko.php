<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengaturanToko extends Model
{
    protected $fillable = [
        'nama_toko', 'logo_path', 'slogan', 'alamat', 'telepon', 'footer_struk', 'format_nota',
        'batas_diskon_kasir_persen', 'izinkan_stok_minus',
        'printer_struk_aktif', 'printer_faktur_aktif',
    ];

    protected function casts(): array
    {
        return [
            'izinkan_stok_minus' => 'boolean',
            'printer_struk_aktif' => 'boolean',
            'printer_faktur_aktif' => 'boolean',
            'batas_diskon_kasir_persen' => 'decimal:2',
        ];
    }

    public function getLogoUrlAttribute(): string
    {
        if ($this->logo_path) {
            if (str_starts_with($this->logo_path, 'http') || str_starts_with($this->logo_path, '/')) {
                return $this->logo_path;
            }
            return asset('storage/' . $this->logo_path);
        }

        return asset('images/logo.png');
    }

    public static function current(): self
    {
        return static::query()->firstOrCreate([], [
            'nama_toko' => 'Rajawali Motor',
            'slogan' => 'Pusat Sparepart & Servis Motor Terpercaya',
            'alamat' => 'Jl. Samanhudi No.102, Jasem, Bulusidokare, Kec. Sidoarjo, Kabupaten Sidoarjo, Jawa Timur 61212',
            'telepon' => '08123456789',
            'footer_struk' => 'Barang yang sudah dibeli tidak dapat ditukar/dikembalikan kecuali ada perjanjian atau garansi resmi. Terima kasih atas kunjungan Anda!',
            'format_nota' => 'PJ{tahun}{urutan}',
            'batas_diskon_kasir_persen' => 5,
            'izinkan_stok_minus' => false,
            'printer_struk_aktif' => false,
            'printer_faktur_aktif' => false,
        ]);
    }
}
