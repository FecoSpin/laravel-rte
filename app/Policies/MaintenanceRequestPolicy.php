<?php

namespace App\Policies;

use App\Models\MaintenanceRequest;
use App\Models\User;

class MaintenanceRequestPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->active;
    }

    public function view(User $user, MaintenanceRequest $request): bool
    {
        if (!$user->active) return false;

        // Admin can view all
        if ($user->isAdmin()) return true;

        // Supervisor can view requests in their zone
        if ($user->isSupervisor() && $user->zone_id === $request->zone_id) return true;

        // Users can view their own requests or assigned requests
        return $user->id === $request->requested_by || $user->id === $request->assigned_to;
    }

    public function create(User $user): bool
    {
        return $user->active;
    }

    public function update(User $user, MaintenanceRequest $request): bool
    {
        if (!$user->active) return false;

        // Admin can update all
        if ($user->isAdmin()) return true;

        // Supervisor can update requests in their zone
        if ($user->isSupervisor() && $user->zone_id === $request->zone_id) return true;

        // Users can update their own requests or assigned requests
        return $user->id === $request->requested_by || $user->id === $request->assigned_to;
    }

    public function delete(User $user, MaintenanceRequest $request): bool
    {
        if (!$user->active) return false;

        // Admin can delete all
        if ($user->isAdmin()) return true;

        // Users can only delete their own pending requests
        return $user->id === $request->requested_by && $request->status === 'pending';
    }

    public function assign(User $user, MaintenanceRequest $request): bool
    {
        if (!$user->active) return false;

        // Only admin and supervisors can assign
        if ($user->isAdmin()) return true;

        // Supervisor can assign requests in their zone
        return $user->isSupervisor() && $user->zone_id === $request->zone_id;
    }

    public function complete(User $user, MaintenanceRequest $request): bool
    {
        if (!$user->active) return false;

        // Admin can complete all
        if ($user->isAdmin()) return true;

        // Supervisor can complete requests in their zone
        if ($user->isSupervisor() && $user->zone_id === $request->zone_id) return true;

        // Assigned technician can complete
        return $user->id === $request->assigned_to;
    }

    public function reject(User $user, MaintenanceRequest $request): bool
    {
        if (!$user->active) return false;

        // Only admin and supervisors can reject
        if ($user->isAdmin()) return true;

        // Supervisor can reject requests in their zone
        return $user->isSupervisor() && $user->zone_id === $request->zone_id;
    }

    public function attachFiles(User $user, MaintenanceRequest $request): bool
    {
        return $this->update($user, $request);
    }
}
