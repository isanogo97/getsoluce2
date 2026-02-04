<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Intervention;
use App\Models\Media;
use Illuminate\Http\Request;
use App\Models\User;

class MediaController extends Controller
{
    public function store(Request $request, Intervention $intervention)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:jpg,jpeg,png,webp'],
        ]);

        $user = $request->user();
        if ($user->role !== User::ROLE_MANAGER && $intervention->user_id !== $user->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $path = $request->file('file')->store('interventions', 'public');

        $media = Media::create([
            'intervention_id' => $intervention->id,
            'file_path' => $path,
        ]);

        return response()->json($media, 201);
    }
}
