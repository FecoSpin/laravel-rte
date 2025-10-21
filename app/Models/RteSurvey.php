<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RteSurvey extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'zone_id',
        'user_id',
        'folio',
        'status',
        'stage',
        'form_data',
        'submitted_at',
        'approved_at',
        'approved_by',
        'rejection_reason',
    ];

    protected $casts = [
        'form_data' => 'array',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function reports()
    {
        return $this->hasMany(RteReport::class, 'survey_id');
    }

    public function attachments()
    {
        return $this->morphMany(ReportAttachment::class, 'attachable');
    }

    /**
     * Scopes
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByStage($query, $stage)
    {
        return $query->where('stage', $stage);
    }

    public function scopeByZone($query, $zoneId)
    {
        return $query->where('zone_id', $zoneId);
    }

    /**
     * Helper methods
     */
    public function isDraft()
    {
        return $this->status === 'draft';
    }

    public function isSubmitted()
    {
        return $this->status === 'submitted';
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }
}
