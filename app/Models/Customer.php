<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = ['nama', 'telepon', 'alamat', 'termin_hari', 'aktif'];

    protected function casts(): array
    {
        return [
            'aktif' => 'boolean',
            'termin_hari' => 'integer',
        ];
    }
}
