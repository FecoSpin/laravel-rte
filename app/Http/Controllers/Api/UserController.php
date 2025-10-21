<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('zone');

        // Filters
        if ($request->has('role')) {
            $query->where('role', $request->get('role'));
        }

        if ($request->has('zone_id')) {
            $query->where('zone_id', $request->get('zone_id'));
        }

        if ($request->has('active')) {
            $query->where('active', $request->boolean('active'));
        }

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Role-based filtering
        $currentUser = $request->user();
        if ($currentUser->role === 'supervisor' && $currentUser->zone_id) {
            $query->where('zone_id', $currentUser->zone_id);
        }

        $users = $query->withCount(['surveys', 'maintenanceRequests'])
                      ->paginate($request->get('per_page', 15));

        return response()->json($users);
    }

    public function store(Request $request)
    {
        $this->authorize('create', User::class);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,supervisor,technician,user',
            'zone_id' => 'nullable|exists:zones,id',
            'active' => 'boolean',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'zone_id' => $request->zone_id,
            'active' => $request->get('active', true),
        ]);

        return response()->json([
            'message' => 'Usuario creado exitosamente',
            'user' => $user->load('zone'),
        ], 201);
    }

    public function show(User $user)
    {
        $this->authorize('view', $user);

        return response()->json([
            'user' => $user->load(['zone'])
                          ->loadCount(['surveys', 'maintenanceRequests', 'assignedMaintenanceRequests']),
        ]);
    }

    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,supervisor,technician,user',
            'zone_id' => 'nullable|exists:zones,id',
            'active' => 'boolean',
        ]);

        $user->update($request->only(['name', 'email', 'role', 'zone_id', 'active']));

        return response()->json([
            'message' => 'Usuario actualizado exitosamente',
            'user' => $user->load('zone'),
        ]);
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        // Check if user has related data
        if ($user->surveys()->count() > 0 || $user->maintenanceRequests()->count() > 0) {
            return response()->json([
                'message' => 'No se puede eliminar el usuario porque tiene datos relacionados',
            ], 422);
        }

        $user->delete();

        return response()->json([
            'message' => 'Usuario eliminado exitosamente',
        ]);
    }

    public function updatePassword(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $request->validate([
            'current_password' => 'required_if:self,true',
            'password' => 'required|string|min:8|confirmed',
            'self' => 'boolean',
        ]);

        // If user is updating their own password, verify current password
        if ($request->boolean('self') && !Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'La contraseña actual es incorrecta',
            ], 422);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'message' => 'Contraseña actualizada exitosamente',
        ]);
    }

    public function toggle(User $user)
    {
        $this->authorize('update', $user);

        $user->update(['active' => !$user->active]);

        return response()->json([
            'message' => $user->active ? 'Usuario activado' : 'Usuario desactivado',
            'user' => $user,
        ]);
    }

    public function profile(Request $request)
    {
        return response()->json([
            'user' => $request->user()->load(['zone'])
                                    ->loadCount(['surveys', 'maintenanceRequests', 'assignedMaintenanceRequests']),
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
        ]);

        $user->update($request->only(['name', 'email']));

        return response()->json([
            'message' => 'Perfil actualizado exitosamente',
            'user' => $user->load('zone'),
        ]);
    }
}
