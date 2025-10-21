<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MaintenanceRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = MaintenanceRequest::with(['zone', 'requestedBy', 'assignedTo']);

        // Filters
        if ($request->has('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->has('priority')) {
            $query->where('priority', $request->get('priority'));
        }

        if ($request->has('zone_id')) {
            $query->where('zone_id', $request->get('zone_id'));
        }

        if ($request->has('assigned_to')) {
            $query->where('assigned_to', $request->get('assigned_to'));
        }

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('folio', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        // Role-based filtering
        $user = $request->user();
        if ($user->role === 'technician') {
            $query->where(function ($q) use ($user) {
                $q->where('requested_by', $user->id)
                  ->orWhere('assigned_to', $user->id);
            });
        } elseif ($user->role === 'supervisor' && $user->zone_id) {
            $query->where('zone_id', $user->zone_id);
        }

        $requests = $query->withCount('attachments')
                         ->latest()
                         ->paginate($request->get('per_page', 15));

        return response()->json($requests);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'zone_id' => 'required|exists:zones,id',
            'location' => 'required|string|max:255',
            'images' => 'nullable|array',
            'images.*' => 'string', // Base64 encoded images or file paths
        ]);

        $maintenanceRequest = MaintenanceRequest::create([
            'title' => $request->title,
            'description' => $request->description,
            'priority' => $request->priority,
            'zone_id' => $request->zone_id,
            'location' => $request->location,
            'images' => $request->images,
            'requested_by' => $request->user()->id,
            'folio' => $this->generateFolio(),
        ]);

        return response()->json([
            'message' => 'Solicitud de mantenimiento creada exitosamente',
            'maintenance_request' => $maintenanceRequest->load(['zone', 'requestedBy']),
        ], 201);
    }

    public function show(MaintenanceRequest $maintenanceRequest)
    {
        $this->authorize('view', $maintenanceRequest);

        return response()->json([
            'maintenance_request' => $maintenanceRequest->load([
                'zone', 'requestedBy', 'assignedTo', 'attachments'
            ]),
        ]);
    }

    public function update(Request $request, MaintenanceRequest $maintenanceRequest)
    {
        $this->authorize('update', $maintenanceRequest);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'location' => 'required|string|max:255',
            'images' => 'nullable|array',
            'resolution_notes' => 'nullable|string',
            'evidence_images' => 'nullable|array',
        ]);

        // Only allow certain updates based on status
        $allowedFields = ['title', 'description', 'priority', 'location'];
        
        if ($maintenanceRequest->status === 'pending') {
            $allowedFields = array_merge($allowedFields, ['images']);
        }

        if (in_array($maintenanceRequest->status, ['in_progress', 'completed'])) {
            $allowedFields = array_merge($allowedFields, ['resolution_notes', 'evidence_images']);
        }

        $maintenanceRequest->update($request->only($allowedFields));

        return response()->json([
            'message' => 'Solicitud actualizada exitosamente',
            'maintenance_request' => $maintenanceRequest,
        ]);
    }

    public function destroy(MaintenanceRequest $maintenanceRequest)
    {
        $this->authorize('delete', $maintenanceRequest);

        // Only allow deletion if request is pending
        if ($maintenanceRequest->status !== 'pending') {
            return response()->json([
                'message' => 'Solo se pueden eliminar solicitudes pendientes',
            ], 422);
        }

        $maintenanceRequest->delete();

        return response()->json([
            'message' => 'Solicitud eliminada exitosamente',
        ]);
    }

    public function assign(Request $request, MaintenanceRequest $maintenanceRequest)
    {
        $this->authorize('assign', $maintenanceRequest);

        $request->validate([
            'assigned_to' => 'required|exists:users,id',
        ]);

        $maintenanceRequest->update([
            'assigned_to' => $request->assigned_to,
            'status' => 'in_progress',
            'assigned_at' => now(),
        ]);

        return response()->json([
            'message' => 'Solicitud asignada exitosamente',
            'maintenance_request' => $maintenanceRequest->load(['assignedTo']),
        ]);
    }

    public function complete(Request $request, MaintenanceRequest $maintenanceRequest)
    {
        $this->authorize('complete', $maintenanceRequest);

        $request->validate([
            'resolution_notes' => 'required|string',
            'evidence_images' => 'nullable|array',
        ]);

        $maintenanceRequest->update([
            'status' => 'completed',
            'resolution_notes' => $request->resolution_notes,
            'evidence_images' => $request->evidence_images,
            'completed_at' => now(),
        ]);

        return response()->json([
            'message' => 'Solicitud completada exitosamente',
            'maintenance_request' => $maintenanceRequest,
        ]);
    }

    public function reject(Request $request, MaintenanceRequest $maintenanceRequest)
    {
        $this->authorize('reject', $maintenanceRequest);

        $request->validate([
            'resolution_notes' => 'required|string',
        ]);

        $maintenanceRequest->update([
            'status' => 'rejected',
            'resolution_notes' => $request->resolution_notes,
        ]);

        return response()->json([
            'message' => 'Solicitud rechazada',
            'maintenance_request' => $maintenanceRequest,
        ]);
    }

    private function generateFolio()
    {
        do {
            $folio = 'MNT-' . date('Y') . '-' . strtoupper(Str::random(6));
        } while (MaintenanceRequest::where('folio', $folio)->exists());

        return $folio;
    }
}
