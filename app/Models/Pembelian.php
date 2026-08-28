<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
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
        'terbayar',
        'status_bayar',
        'jatuh_tempo',
        'tanggal_lunas',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jatuh_tempo' => 'date',
        'tanggal_lunas' => 'datetime',
        'total' => 'decimal:2',
        'terbayar' => 'decimal:2',
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

    public function pembayarans(): HasMany
    {
        return $this->hasMany(PembelianPembayaran::class)->orderBy('tanggal_bayar', 'desc');
    }

    public function getSisaHutangAttribute(): float
    {
        return max(0.0, (float) $this->total - (float) ($this->terbayar ?? 0));
    }

    public function getIsOverdueAttribute(): bool
    {
        if ($this->status_bayar === 'lunas' || !$this->jatuh_tempo) {
            return false;
        }

        return now()->startOfDay()->greaterThan($this->jatuh_tempo->startOfDay());
    }

    public function getHariTerlambatAttribute(): int
    {
        if (!$this->is_overdue || !$this->jatuh_tempo) {
            return 0;
        }

        return (int) $this->jatuh_tempo->startOfDay()->diffInDays(now()->startOfDay());
    }

    public function getSisaHariJatuhTempoAttribute(): int
    {
        if ($this->status_bayar === 'lunas' || !$this->jatuh_tempo || $this->is_overdue) {
            return 0;
        }

        return (int) now()->startOfDay()->diffInDays($this->jatuh_tempo->startOfDay());
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
