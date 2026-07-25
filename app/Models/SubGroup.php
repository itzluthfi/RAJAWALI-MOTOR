<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubGroup extends Model
{
    protected $fillable = ['group_id', 'nama'];

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }
}
