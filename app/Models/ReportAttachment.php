<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'attachable_type',
        'attachable_id',
        'filename',
        'original_name',
        'mime_type',
        'size',
        'path',
        'type',
    ];

    protected $casts = [
        'size' => 'integer',
    ];

    /**
     * Relationships
     */
    public function attachable()
    {
        return $this->morphTo();
    }

    /**
     * Scopes
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeImages($query)
    {
        return $query->where('type', 'image');
    }

    public function scopeDocuments($query)
    {
        return $query->where('type', 'document');
    }

    public function scopePdfs($query)
    {
        return $query->where('type', 'pdf');
    }

    /**
     * Helper methods
     */
    public function isImage()
    {
        return $this->type === 'image';
    }

    public function isDocument()
    {
        return $this->type === 'document';
    }

    public function isPdf()
    {
        return $this->type === 'pdf';
    }

    public function getFileSizeFormatted()
    {
        $bytes = $this->size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function getFullPath()
    {
        return storage_path('app/' . $this->path);
    }

    public function getUrl()
    {
        return asset('storage/' . $this->path);
    }
}
