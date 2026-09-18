<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class JobCard extends Model {
    use HasUuids;

    protected $fillable = [
        'workshop_id', 'vehicle_number', 'customer_name', 
        'customer_phone', 'tracking_token', 'status', 
        'estimated_amount', 'notes'
    ];

    protected static function booted(): void {
        static::creating(function ($jobCard) {
            $jobCard->tracking_token = (string) Str::uuid();
        });
    }

    public function workshop(): BelongsTo {
        return $this->belongsTo(Workshop::class);
    }

    public function media(): HasMany {
        return $this->hasMany(JobCardMedia::class);
    }
}
?>