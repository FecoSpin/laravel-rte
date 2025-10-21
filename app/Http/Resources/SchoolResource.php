<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'cct' => $this->cct,
            'direction' => $this->direction,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            
            // Relationships
            'zone' => new ZoneResource($this->whenLoaded('zone')),
            'surveys' => RteSurveyResource::collection($this->whenLoaded('surveys')),
            
            // Counts
            'surveys_count' => $this->whenCounted('surveys'),
        ];
    }
}
