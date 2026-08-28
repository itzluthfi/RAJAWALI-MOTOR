<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PembelianPembayaran extends Model
{
    protected $table = 'pembelian_pembayarans';

    protected $fillable = [
        'pembelian_id',
        'user_id',
        'tanggal_bayar',
        'nominal',
        'sumber',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_bayar' => 'datetime',
        'nominal' => 'decimal:2',
    ];

    public function pembelian(): BelongsTo
    {
        return $this->belongsTo(Pembelian::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
