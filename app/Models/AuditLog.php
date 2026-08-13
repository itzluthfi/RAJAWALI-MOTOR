<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id',
        'nama_user',
        'aksi',
        'modul',
        'objek',
        'ip_address',
        'perubahan',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function catat(string $aksi, string $modul = 'Umum', ?string $objek = null, ?string $perubahan = null): self
    {
        $user = Auth::user();

        return static::create([
            'user_id' => $user?->id,
            'nama_user' => $user?->name ?? 'Sistem / Guest',
            'aksi' => $aksi,
            'modul' => $modul,
            'objek' => $objek,
            'ip_address' => request()->ip(),
            'perubahan' => $perubahan,
        ]);
    }
}
