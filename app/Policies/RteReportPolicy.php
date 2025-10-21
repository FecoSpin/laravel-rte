<?php

namespace App\Policies;

use App\Models\RteReport;
use App\Models\User;

class RteReportPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->active;
    }

    public function view(User $user, RteReport $report): bool
    {
        if (!$user->active) return false;

        // Admin can view all
        if ($user->isAdmin()) return true;

        // Supervisor can view reports in their zone
        if ($user->isSupervisor() && $user->zone_id === $report->survey->zone_id) return true;

        // Users can view reports for their own surveys
        return $user->id === $report->survey->user_id;
    }

    public function create(User $user): bool
    {
        return $user->active;
    }

    public function update(User $user, RteReport $report): bool
    {
        if (!$user->active) return false;

        // Admin can update all
        if ($user->isAdmin()) return true;

        // Users can only update reports for their own surveys
        return $user->id === $report->survey->user_id;
    }

    public function delete(User $user, RteReport $report): bool
    {
        if (!$user->active) return false;

        // Admin can delete all
        if ($user->isAdmin()) return true;

        // Users can only delete reports for their own surveys
        return $user->id === $report->survey->user_id;
    }

    public function approve(User $user, RteReport $report): bool
    {
        if (!$user->active) return false;

        // Only admin and supervisors can approve
        if ($user->isAdmin()) return true;

        // Supervisor can approve reports in their zone
        return $user->isSupervisor() && $user->zone_id === $report->survey->zone_id;
    }

    public function attachFiles(User $user, RteReport $report): bool
    {
        return $this->update($user, $report);
    }
}
