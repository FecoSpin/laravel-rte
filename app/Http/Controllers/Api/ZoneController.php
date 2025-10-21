<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Zone;
use Illuminate\Http\Request;

class ZoneController extends Controller
{
    public function index(Request $request)
    {
        $query = Zone::query();

        if ($request->has('active')) {
            $query->where('active', $request->boolean('active'));
        }

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $zones = $query->withCount(['users', 'surveys', 'maintenanceRequests'])
                      ->paginate($request->get('per_page', 15));

        return response()->json($zones);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:zones',
            'description' => 'nullable|string',
            'active' => 'boolean',
        ]);

        $zone = Zone::create($request->all());

        return response()->json([
            'message' => 'Zona creada exitosamente',
            'zone' => $zone,
        ], 201);
    }

    public function show(Zone $zone)
    {
        return response()->json([
            'zone' => $zone->load(['users', 'schools'])
                          ->loadCount(['surveys', 'maintenanceRequests']),
        ]);
    }

    public function update(Request $request, Zone $zone)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:zones,code,' . $zone->id,
            'description' => 'nullable|string',
            'active' => 'boolean',
        ]);

        $zone->update($request->all());

        return response()->json([
            'message' => 'Zona actualizada exitosamente',
            'zone' => $zone,
        ]);
    }

    public function destroy(Zone $zone)
    {
        // Check if zone has related data
        if ($zone->users()->count() > 0 || $zone->surveys()->count() > 0) {
            return response()->json([
                'message' => 'No se puede eliminar la zona porque tiene datos relacionados',
            ], 422);
        }

        $zone->delete();

        return response()->json([
            'message' => 'Zona eliminada exitosamente',
        ]);
    }

    public function toggle(Zone $zone)
    {
        $zone->update(['active' => !$zone->active]);

        return response()->json([
            'message' => $zone->active ? 'Zona activada' : 'Zona desactivada',
            'zone' => $zone,
        ]);
    }
}
