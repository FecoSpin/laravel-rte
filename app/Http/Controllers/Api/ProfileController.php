<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();

        $profile = $user->profile ?: new Profile(['user_id' => $user->id]);

        return response()->json([
            'user' => $user,
            'profile' => $profile,
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'work_center_name'   => 'nullable|string|max:255',
            'cct'                => 'nullable|string|max:50',
            'zone'               => 'nullable|string|max:100',
            'rte_name'           => 'nullable|string|max:255',
            'shift'              => 'nullable|string|max:100',
            'sector'             => 'nullable|string|max:100',
            'report_period'      => 'nullable|string|max:255',
            'commissioned_hours' => 'nullable|integer|min:0',
        ]);

        $profile = $user->profile;

        if (!$profile) {
            $profile = new Profile(['user_id' => $user->id]);
        }

        $profile->fill($data);
        $profile->user_id = $user->id;
        $profile->save();

        return response()->json([
            'message' => 'Perfil actualizado correctamente',
            'profile' => $profile,
        ]);
    }
}
