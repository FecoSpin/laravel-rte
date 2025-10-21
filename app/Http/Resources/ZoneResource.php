<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ZoneResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'description' => $this->description,
            'active' => $this->active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            
            // Relationships
            'users_count' => $this->whenCounted('users'),
            'surveys_count' => $this->whenCounted('surveys'),
            'maintenance_requests_count' => $this->whenCounted('maintenanceRequests'),
            'schools_count' => $this->whenCounted('schools'),
            
            'users' => UserResource::collection($this->whenLoaded('users')),
            'schools' => SchoolResource::collection($this->whenLoaded('schools')),
        ];
    }
}
