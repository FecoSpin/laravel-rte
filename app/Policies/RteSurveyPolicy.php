<?php

namespace App\Policies;

use App\Models\RteSurvey;
use App\Models\User;

class RteSurveyPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->active;
    }

    public function view(User $user, RteSurvey $survey): bool
    {
        if (!$user->active) return false;

        // Admin can view all
        if ($user->isAdmin()) return true;

        // Supervisor can view surveys in their zone
        if ($user->isSupervisor() && $user->zone_id === $survey->zone_id) return true;

        // Users can view their own surveys
        return $user->id === $survey->user_id;
    }

    public function create(User $user): bool
    {
        return $user->active;
    }

    public function update(User $user, RteSurvey $survey): bool
    {
        if (!$user->active) return false;

        // Admin can update all
        if ($user->isAdmin()) return true;

        // Users can only update their own surveys
        return $user->id === $survey->user_id;
    }

    public function delete(User $user, RteSurvey $survey): bool
    {
        if (!$user->active) return false;

        // Admin can delete all
        if ($user->isAdmin()) return true;

        // Users can only delete their own surveys
        return $user->id === $survey->user_id;
    }

    public function approve(User $user, RteSurvey $survey): bool
    {
        if (!$user->active) return false;

        // Only admin and supervisors can approve
        if ($user->isAdmin()) return true;

        // Supervisor can approve surveys in their zone
        return $user->isSupervisor() && $user->zone_id === $survey->zone_id;
    }

    public function createReport(User $user, RteSurvey $survey): bool
    {
        if (!$user->active) return false;

        // Admin can create reports for any survey
        if ($user->isAdmin()) return true;

        // Users can create reports for their own surveys
        return $user->id === $survey->user_id;
    }

    public function attachFiles(User $user, RteSurvey $survey): bool
    {
        return $this->update($user, $survey);
    }
}
