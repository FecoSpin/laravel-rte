<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MaintenanceRequestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'folio' => $this->folio,
            'title' => $this->title,
            'description' => $this->description,
            'priority' => $this->priority,
            'status' => $this->status,
            'location' => $this->location,
            'images' => $this->images,
            'evidence_images' => $this->evidence_images,
            'resolution_notes' => $this->resolution_notes,
            'assigned_at' => $this->assigned_at,
            'completed_at' => $this->completed_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            
            // Relationships
            'zone' => new ZoneResource($this->whenLoaded('zone')),
            'requested_by' => new UserResource($this->whenLoaded('requestedBy')),
            'assigned_to' => new UserResource($this->whenLoaded('assignedTo')),
            'attachments' => ReportAttachmentResource::collection($this->whenLoaded('attachments')),
            
            // Counts
            'attachments_count' => $this->whenCounted('attachments'),
        ];
    }
}
