<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pembelian extends Model
{
    protected $fillable = [
        'nomor_pembelian',
        'supplier_id',
        'user_id',
        'nomor_faktur_supplier',
        'tanggal',
        'total',
        'status_bayar',
        'jatuh_tempo',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jatuh_tempo' => 'date',
        'total' => 'decimal:2',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(PembelianDetail::class);
    }

    public static function buatNomorPembelian(): string
    {
        $prefix = 'PB' . date('Ym');
        $terakhir = static::where('nomor_pembelian', 'LIKE', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->first();

        if (! $terakhir) {
            return $prefix . '0001';
        }

        $urutan = (int) substr($terakhir->nomor_pembelian, -4);
        return $prefix . str_pad((string) ($urutan + 1), 4, '0', STR_PAD_LEFT);
    }
}
