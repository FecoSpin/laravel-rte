<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RteReportResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'report_type' => $this->report_type,
            'title' => $this->title,
            'content' => $this->content,
            'status' => $this->status,
            'pdf_path' => $this->pdf_path,
            'version' => $this->version,
            'submitted_at' => $this->submitted_at,
            'approved_at' => $this->approved_at,
            'rejection_reason' => $this->rejection_reason,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            
            // Relationships
            'survey' => new RteSurveyResource($this->whenLoaded('survey')),
            'approved_by' => new UserResource($this->whenLoaded('approvedBy')),
            'attachments' => ReportAttachmentResource::collection($this->whenLoaded('attachments')),
            
            // Counts
            'attachments_count' => $this->whenCounted('attachments'),
        ];
    }
}
