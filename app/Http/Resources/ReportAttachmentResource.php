<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportAttachmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'filename' => $this->filename,
            'original_name' => $this->original_name,
            'mime_type' => $this->mime_type,
            'size' => $this->size,
            'size_formatted' => $this->getFileSizeFormatted(),
            'path' => $this->path,
            'type' => $this->type,
            'url' => $this->getUrl(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            
            // Polymorphic relationship info
            'attachable_type' => $this->attachable_type,
            'attachable_id' => $this->attachable_id,
        ];
    }
}
