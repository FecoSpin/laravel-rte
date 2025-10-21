<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RteReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'survey_id',
        'report_type',
        'title',
        'content',
        'status',
        'pdf_path',
        'version',
        'submitted_at',
        'approved_at',
        'approved_by',
        'rejection_reason',
    ];

    protected $casts = [
        'content' => 'array',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
        'version' => 'integer',
    ];

    /**
     * Relationships
     */
    public function survey()
    {
        return $this->belongsTo(RteSurvey::class, 'survey_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
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

    public function scopeByType($query, $type)
    {
        return $query->where('report_type', $type);
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
