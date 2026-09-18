<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Workshop extends Model
{
    use HasUuids;

    protected $fillable = [
        'name',
        'phone',
        'address',
    ];

    public function jobCards(): HasMany
    {
        return $this->hasMany(JobCard::class);
    }
}
