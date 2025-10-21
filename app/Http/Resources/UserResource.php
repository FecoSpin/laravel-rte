<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'active' => $this->active,
            'email_verified_at' => $this->email_verified_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            
            // Relationships
            'zone' => new ZoneResource($this->whenLoaded('zone')),
            
            // Counts
            'surveys_count' => $this->whenCounted('surveys'),
            'maintenance_requests_count' => $this->whenCounted('maintenanceRequests'),
            'assigned_maintenance_requests_count' => $this->whenCounted('assignedMaintenanceRequests'),
        ];
    }
}
