<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RteSurveyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'folio' => $this->folio,
            'status' => $this->status,
            'stage' => $this->stage,
            'form_data' => $this->form_data,
            'submitted_at' => $this->submitted_at,
            'approved_at' => $this->approved_at,
            'rejection_reason' => $this->rejection_reason,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            
            // Relationships
            'zone' => new ZoneResource($this->whenLoaded('zone')),
            'user' => new UserResource($this->whenLoaded('user')),
            'approved_by' => new UserResource($this->whenLoaded('approvedBy')),
            'reports' => RteReportResource::collection($this->whenLoaded('reports')),
            'attachments' => ReportAttachmentResource::collection($this->whenLoaded('attachments')),
            
            // Counts
            'reports_count' => $this->whenCounted('reports'),
            'attachments_count' => $this->whenCounted('attachments'),
        ];
    }
}
