<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ReportFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReportFileController extends Controller
{
    public function store(Request $request, string $reportType)
    {
        $user = $request->user();

        $validated = $request->validate([
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $file = $request->file('file');

        $path = $file->store('report-files', 'public');

        $reportFile = ReportFile::create([
            'user_id'       => $user->id,
            'report_type'   => $reportType,
            'original_name' => $file->getClientOriginalName(),
            'path'          => $path,
            'mime_type'     => $file->getClientMimeType(),
            'size'          => $file->getSize(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Archivo subido correctamente',
            'data'    => $reportFile,
        ], 201);
    }
}
