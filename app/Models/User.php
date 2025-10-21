<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'zone_id',
        'active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'active' => 'boolean',
        ];
    }

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

    public function approvedSurveys()
    {
        return $this->hasMany(RteSurvey::class, 'approved_by');
    }

    public function approvedReports()
    {
        return $this->hasMany(RteReport::class, 'approved_by');
    }

    public function maintenanceRequests()
    {
        return $this->hasMany(MaintenanceRequest::class, 'requested_by');
    }

    public function assignedMaintenanceRequests()
    {
        return $this->hasMany(MaintenanceRequest::class, 'assigned_to');
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Helper methods
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isSupervisor()
    {
        return $this->role === 'supervisor';
    }

    public function isTechnician()
    {
        return $this->role === 'technician';
    }

    public function hasRole($role)
    {
        return $this->role === $role;
    }
}
