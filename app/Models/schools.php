<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'cct',
        'direction',
        'zone_id',
    ];

    /**
     * Relationships
     */
    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    public function surveys()
    {
        return $this->hasMany(RteSurvey::class);
    }

    /**
     * Scopes
     */
    public function scopeByZone($query, $zoneId)
    {
        return $query->where('zone_id', $zoneId);
    }
}
