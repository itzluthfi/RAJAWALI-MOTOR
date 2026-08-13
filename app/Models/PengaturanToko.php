<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengaturanToko extends Model
{
    protected $fillable = [
        'nama_toko', 'alamat', 'telepon', 'format_nota',
        'batas_diskon_kasir_persen', 'izinkan_stok_minus',
    ];

    protected function casts(): array
    {
        return [
            'izinkan_stok_minus' => 'boolean',
            'batas_diskon_kasir_persen' => 'decimal:2',
        ];
    }

    /**
     * Baris pengaturan aktif (singleton). Dibuat otomatis dengan nilai
     * default jika belum ada baris sama sekali.
     */
    public static function current(): self
    {
        return static::query()->firstOrCreate([], [
            'nama_toko' => 'Rajawali Motor',
            'alamat' => 'Jl. Samanhudi No.102, Jasem, Bulusidokare, Kec. Sidoarjo, Kabupaten Sidoarjo, Jawa Timur 61212',
            'telepon' => null,
            'format_nota' => 'PJ{tahun}{urutan}',
            'batas_diskon_kasir_persen' => 5,
            'izinkan_stok_minus' => false,
        ]);
    }
}
