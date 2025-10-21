<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RteSurvey;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RteSurveyController extends Controller
{
    public function index(Request $request)
    {
        $query = RteSurvey::with(['zone', 'user', 'approvedBy']);

        // Filters
        if ($request->has('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->has('stage')) {
            $query->where('stage', $request->get('stage'));
        }

        if ($request->has('zone_id')) {
            $query->where('zone_id', $request->get('zone_id'));
        }

        if ($request->has('user_id')) {
            $query->where('user_id', $request->get('user_id'));
        }

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('folio', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Role-based filtering
        $user = $request->user();
        if ($user->role === 'technician') {
            $query->where('user_id', $user->id);
        } elseif ($user->role === 'supervisor' && $user->zone_id) {
            $query->where('zone_id', $user->zone_id);
        }

        $surveys = $query->withCount(['reports', 'attachments'])
                        ->latest()
                        ->paginate($request->get('per_page', 15));

        return response()->json($surveys);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'zone_id' => 'required|exists:zones,id',
            'form_data' => 'nullable|array',
        ]);

        $survey = RteSurvey::create([
            'title' => $request->title,
            'description' => $request->description,
            'zone_id' => $request->zone_id,
            'user_id' => $request->user()->id,
            'folio' => $this->generateFolio(),
            'form_data' => $request->form_data,
        ]);

        return response()->json([
            'message' => 'Encuesta RTE creada exitosamente',
            'survey' => $survey->load(['zone', 'user']),
        ], 201);
    }

    public function show(RteSurvey $survey)
    {
        $this->authorize('view', $survey);

        return response()->json([
            'survey' => $survey->load(['zone', 'user', 'approvedBy', 'reports', 'attachments']),
        ]);
    }

    public function update(Request $request, RteSurvey $survey)
    {
        $this->authorize('update', $survey);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'zone_id' => 'required|exists:zones,id',
            'form_data' => 'nullable|array',
        ]);

        // Only allow updates if survey is in draft status
        if ($survey->status !== 'draft') {
            return response()->json([
                'message' => 'Solo se pueden editar encuestas en estado borrador',
            ], 422);
        }

        $survey->update($request->only(['title', 'description', 'zone_id', 'form_data']));

        return response()->json([
            'message' => 'Encuesta actualizada exitosamente',
            'survey' => $survey->load(['zone', 'user']),
        ]);
    }

    public function destroy(RteSurvey $survey)
    {
        $this->authorize('delete', $survey);

        // Only allow deletion if survey is in draft status
        if ($survey->status !== 'draft') {
            return response()->json([
                'message' => 'Solo se pueden eliminar encuestas en estado borrador',
            ], 422);
        }

        $survey->delete();

        return response()->json([
            'message' => 'Encuesta eliminada exitosamente',
        ]);
    }

    public function submit(RteSurvey $survey)
    {
        $this->authorize('update', $survey);

        if ($survey->status !== 'draft') {
            return response()->json([
                'message' => 'La encuesta ya ha sido enviada',
            ], 422);
        }

        $survey->update([
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        return response()->json([
            'message' => 'Encuesta enviada para revisión',
            'survey' => $survey,
        ]);
    }

    public function approve(Request $request, RteSurvey $survey)
    {
        $this->authorize('approve', $survey);

        $request->validate([
            'stage' => 'nullable|in:initial,first_report,second_report,final_report',
        ]);

        $survey->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => $request->user()->id,
            'stage' => $request->get('stage', $survey->stage),
        ]);

        return response()->json([
            'message' => 'Encuesta aprobada exitosamente',
            'survey' => $survey->load(['approvedBy']),
        ]);
    }

    public function reject(Request $request, RteSurvey $survey)
    {
        $this->authorize('approve', $survey);

        $request->validate([
            'rejection_reason' => 'required|string',
        ]);

        $survey->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        return response()->json([
            'message' => 'Encuesta rechazada',
            'survey' => $survey,
        ]);
    }

    private function generateFolio()
    {
        do {
            $folio = 'RTE-' . date('Y') . '-' . strtoupper(Str::random(6));
        } while (RteSurvey::where('folio', $folio)->exists());

        return $folio;
    }
}
