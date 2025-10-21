<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'folio',
        'title',
        'description',
        'priority',
        'status',
        'zone_id',
        'requested_by',
        'assigned_to',
        'location',
        'images',
        'evidence_images',
        'resolution_notes',
        'assigned_at',
        'completed_at',
    ];

    protected $casts = [
        'images' => 'array',
        'evidence_images' => 'array',
        'assigned_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
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

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeByZone($query, $zoneId)
    {
        return $query->where('zone_id', $zoneId);
    }

    public function scopeAssignedTo($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    public function scopeRequestedBy($query, $userId)
    {
        return $query->where('requested_by', $userId);
    }

    /**
     * Helper methods
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isInProgress()
    {
        return $this->status === 'in_progress';
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    public function isHighPriority()
    {
        return $this->priority === 'high' || $this->priority === 'urgent';
    }
}
