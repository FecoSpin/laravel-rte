<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ReportAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ReportAttachmentController extends Controller
{
    public function index(Request $request)
    {
        $query = ReportAttachment::with('attachable');

        // Filters
        if ($request->has('type')) {
            $query->where('type', $request->get('type'));
        }

        if ($request->has('attachable_type')) {
            $query->where('attachable_type', $request->get('attachable_type'));
        }

        if ($request->has('attachable_id')) {
            $query->where('attachable_id', $request->get('attachable_id'));
        }

        $attachments = $query->latest()->paginate($request->get('per_page', 15));

        return response()->json($attachments);
    }

    public function store(Request $request)
    {
        $request->validate([
            'attachable_type' => 'required|string|in:App\Models\RteSurvey,App\Models\RteReport,App\Models\MaintenanceRequest',
            'attachable_id' => 'required|integer',
            'file' => 'required|file|max:10240', // 10MB max
            'type' => 'nullable|in:image,document,pdf,other',
        ]);

        // Verify the attachable model exists
        $attachableClass = $request->attachable_type;
        $attachable = $attachableClass::findOrFail($request->attachable_id);

        // Check authorization based on attachable type
        $this->authorize('attachFiles', $attachable);

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $mimeType = $file->getMimeType();
        $size = $file->getSize();

        // Generate unique filename
        $filename = Str::uuid() . '.' . $extension;
        
        // Determine file type
        $type = $request->get('type', $this->determineFileType($mimeType));

        // Store file
        $path = $file->storeAs('attachments', $filename, 'public');

        $attachment = ReportAttachment::create([
            'attachable_type' => $request->attachable_type,
            'attachable_id' => $request->attachable_id,
            'filename' => $filename,
            'original_name' => $originalName,
            'mime_type' => $mimeType,
            'size' => $size,
            'path' => $path,
            'type' => $type,
        ]);

        return response()->json([
            'message' => 'Archivo subido exitosamente',
            'attachment' => $attachment,
        ], 201);
    }

    public function show(ReportAttachment $attachment)
    {
        $this->authorize('view', $attachment->attachable);

        return response()->json([
            'attachment' => $attachment->load('attachable'),
        ]);
    }

    public function download(ReportAttachment $attachment)
    {
        $this->authorize('view', $attachment->attachable);

        $filePath = storage_path('app/public/' . $attachment->path);

        if (!file_exists($filePath)) {
            return response()->json([
                'message' => 'Archivo no encontrado',
            ], 404);
        }

        return response()->download($filePath, $attachment->original_name);
    }

    public function destroy(ReportAttachment $attachment)
    {
        $this->authorize('delete', $attachment->attachable);

        // Delete file from storage
        if (Storage::disk('public')->exists($attachment->path)) {
            Storage::disk('public')->delete($attachment->path);
        }

        $attachment->delete();

        return response()->json([
            'message' => 'Archivo eliminado exitosamente',
        ]);
    }

    public function bulkUpload(Request $request)
    {
        $request->validate([
            'attachable_type' => 'required|string|in:App\Models\RteSurvey,App\Models\RteReport,App\Models\MaintenanceRequest',
            'attachable_id' => 'required|integer',
            'files' => 'required|array|max:10',
            'files.*' => 'file|max:10240', // 10MB max per file
        ]);

        // Verify the attachable model exists
        $attachableClass = $request->attachable_type;
        $attachable = $attachableClass::findOrFail($request->attachable_id);

        // Check authorization
        $this->authorize('attachFiles', $attachable);

        $attachments = [];
        $errors = [];

        foreach ($request->file('files') as $index => $file) {
            try {
                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $mimeType = $file->getMimeType();
                $size = $file->getSize();

                // Generate unique filename
                $filename = Str::uuid() . '.' . $extension;
                
                // Determine file type
                $type = $this->determineFileType($mimeType);

                // Store file
                $path = $file->storeAs('attachments', $filename, 'public');

                $attachment = ReportAttachment::create([
                    'attachable_type' => $request->attachable_type,
                    'attachable_id' => $request->attachable_id,
                    'filename' => $filename,
                    'original_name' => $originalName,
                    'mime_type' => $mimeType,
                    'size' => $size,
                    'path' => $path,
                    'type' => $type,
                ]);

                $attachments[] = $attachment;
            } catch (\Exception $e) {
                $errors[] = [
                    'file' => $file->getClientOriginalName(),
                    'error' => 'Error al subir el archivo: ' . $e->getMessage(),
                ];
            }
        }

        return response()->json([
            'message' => count($attachments) . ' archivos subidos exitosamente',
            'attachments' => $attachments,
            'errors' => $errors,
        ], 201);
    }

    private function determineFileType($mimeType)
    {
        if (str_starts_with($mimeType, 'image/')) {
            return 'image';
        }

        if ($mimeType === 'application/pdf') {
            return 'pdf';
        }

        if (in_array($mimeType, [
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/plain',
            'text/csv',
        ])) {
            return 'document';
        }

        return 'other';
    }
}
