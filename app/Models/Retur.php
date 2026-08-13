<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Retur extends Model
{
    protected $fillable = [
        'nomor_retur',
        'jenis',
        'penjualan_id',
        'pembelian_id',
        'customer_id',
        'supplier_id',
        'user_id',
        'tanggal',
        'total',
        'alasan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'total' => 'decimal:2',
    ];

    public function penjualan(): BelongsTo
    {
        return $this->belongsTo(Penjualan::class);
    }

    public function pembelian(): BelongsTo
    {
        return $this->belongsTo(Pembelian::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

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
        return $this->hasMany(ReturDetail::class);
    }

    public static function buatNomorRetur(string $jenis): string
    {
        $prefix = ($jenis === 'penjualan' ? 'RJ' : 'RB') . date('Ym');
        $terakhir = static::where('nomor_retur', 'LIKE', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->first();

        if (! $terakhir) {
            return $prefix . '0001';
        }

        $urutan = (int) substr($terakhir->nomor_retur, -4);
        return $prefix . str_pad((string) ($urutan + 1), 4, '0', STR_PAD_LEFT);
    }
}
