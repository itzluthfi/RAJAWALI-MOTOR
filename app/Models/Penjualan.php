<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Penjualan extends Model
{
    protected $fillable = [
        'nomor_nota', 'customer_id', 'user_id', 'subtotal',
        'diskon', 'pajak', 'total_akhir', 'bayar', 'kembali',
        'metode_pembayaran', 'status_bayar', 'catatan',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'diskon' => 'decimal:2',
            'pajak' => 'decimal:2',
            'total_akhir' => 'decimal:2',
            'bayar' => 'decimal:2',
            'kembali' => 'decimal:2',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(PenjualanDetail::class);
    }

    /**
     * Generator nomor nota otomatis (contoh: PJ2026080001)
     */
    public static function buatNomorNota(): string
    {
        $pengaturan = PengaturanToko::current();
        $prefix = 'PJ' . date('Ym');
        $last = static::query()->where('nomor_nota', 'LIKE', "{$prefix}%")->orderByDesc('id')->first();

        if ($last) {
            $lastNumber = (int) substr($last->nomor_nota, -4);
            $newNumber = str_pad((string) ($lastNumber + 1), 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return "{$prefix}{$newNumber}";
    }
}
