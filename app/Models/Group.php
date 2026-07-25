<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Group extends Model
{
    protected $fillable = ['nama'];

    public function subGroups(): HasMany
    {
        return $this->hasMany(SubGroup::class);
    }
}
