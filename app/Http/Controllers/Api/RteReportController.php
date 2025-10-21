<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RteReport;
use App\Models\RteSurvey;
use Illuminate\Http\Request;

class RteReportController extends Controller
{
    public function index(Request $request)
    {
        $query = RteReport::with(['survey.zone', 'survey.user', 'approvedBy']);

        // Filters
        if ($request->has('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->has('report_type')) {
            $query->where('report_type', $request->get('report_type'));
        }

        if ($request->has('survey_id')) {
            $query->where('survey_id', $request->get('survey_id'));
        }

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('survey', function ($sq) use ($search) {
                      $sq->where('folio', 'like', "%{$search}%");
                  });
            });
        }

        // Role-based filtering
        $user = $request->user();
        if ($user->role === 'technician') {
            $query->whereHas('survey', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        } elseif ($user->role === 'supervisor' && $user->zone_id) {
            $query->whereHas('survey', function ($q) use ($user) {
                $q->where('zone_id', $user->zone_id);
            });
        }

        $reports = $query->withCount('attachments')
                        ->latest()
                        ->paginate($request->get('per_page', 15));

        return response()->json($reports);
    }

    public function store(Request $request)
    {
        $request->validate([
            'survey_id' => 'required|exists:rte_surveys,id',
            'report_type' => 'required|in:first,second,final',
            'title' => 'required|string|max:255',
            'content' => 'required|array',
        ]);

        $survey = RteSurvey::findOrFail($request->survey_id);
        $this->authorize('createReport', $survey);

        // Check if report type already exists for this survey
        $existingReport = RteReport::where('survey_id', $survey->id)
                                  ->where('report_type', $request->report_type)
                                  ->first();

        if ($existingReport) {
            return response()->json([
                'message' => 'Ya existe un reporte de este tipo para esta encuesta',
            ], 422);
        }

        $report = RteReport::create([
            'survey_id' => $survey->id,
            'report_type' => $request->report_type,
            'title' => $request->title,
            'content' => $request->content,
        ]);

        return response()->json([
            'message' => 'Reporte creado exitosamente',
            'report' => $report->load(['survey']),
        ], 201);
    }

    public function show(RteReport $report)
    {
        $this->authorize('view', $report);

        return response()->json([
            'report' => $report->load(['survey.zone', 'survey.user', 'approvedBy', 'attachments']),
        ]);
    }

    public function update(Request $request, RteReport $report)
    {
        $this->authorize('update', $report);

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|array',
        ]);

        // Only allow updates if report is in draft status
        if ($report->status !== 'draft') {
            return response()->json([
                'message' => 'Solo se pueden editar reportes en estado borrador',
            ], 422);
        }

        $report->update([
            'title' => $request->title,
            'content' => $request->content,
            'version' => $report->version + 1,
        ]);

        return response()->json([
            'message' => 'Reporte actualizado exitosamente',
            'report' => $report,
        ]);
    }

    public function destroy(RteReport $report)
    {
        $this->authorize('delete', $report);

        // Only allow deletion if report is in draft status
        if ($report->status !== 'draft') {
            return response()->json([
                'message' => 'Solo se pueden eliminar reportes en estado borrador',
            ], 422);
        }

        $report->delete();

        return response()->json([
            'message' => 'Reporte eliminado exitosamente',
        ]);
    }

    public function submit(RteReport $report)
    {
        $this->authorize('update', $report);

        if ($report->status !== 'draft') {
            return response()->json([
                'message' => 'El reporte ya ha sido enviado',
            ], 422);
        }

        $report->update([
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        return response()->json([
            'message' => 'Reporte enviado para revisión',
            'report' => $report,
        ]);
    }

    public function approve(Request $request, RteReport $report)
    {
        $this->authorize('approve', $report);

        $report->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => $request->user()->id,
        ]);

        return response()->json([
            'message' => 'Reporte aprobado exitosamente',
            'report' => $report->load(['approvedBy']),
        ]);
    }

    public function reject(Request $request, RteReport $report)
    {
        $this->authorize('approve', $report);

        $request->validate([
            'rejection_reason' => 'required|string',
        ]);

        $report->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        return response()->json([
            'message' => 'Reporte rechazado',
            'report' => $report,
        ]);
    }

    public function generatePdf(RteReport $report)
    {
        $this->authorize('view', $report);

        // TODO: Implement PDF generation logic
        // This would typically use a package like dompdf or tcpdf

        return response()->json([
            'message' => 'Funcionalidad de PDF en desarrollo',
        ], 501);
    }
}
