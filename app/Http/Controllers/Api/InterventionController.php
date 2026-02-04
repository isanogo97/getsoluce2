<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Intervention;
use App\Models\Site;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class InterventionController extends Controller
{
    public function index(Request $request)
    {
        $query = Intervention::query()->with(['site', 'user', 'media']);

        if ($request->user()->role !== User::ROLE_MANAGER) {
            $query->where('user_id', $request->user()->id);
        }

        if ($request->filled('site_id')) {
            $query->where('site_id', $request->integer('site_id'));
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->integer('user_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('week_start')) {
            $start = Carbon::parse($request->string('week_start'))->startOfWeek();
            $end = (clone $start)->endOfWeek();
            $query->whereBetween('created_at', [$start, $end]);
        }

        $interventions = $query->latest()->paginate(25);

        return response()->json($interventions);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'site_id' => ['required', 'exists:sites,id'],
            'status' => ['required', 'string'],
            'problem_resolved' => ['nullable', 'boolean'],
            'unresolved_reason' => ['nullable', 'string', 'max:255'],
            'comment' => ['nullable', 'string'],
        ]);

        $site = Site::findOrFail($data['site_id']);

        $intervention = Intervention::create([
            'site_id' => $site->id,
            'user_id' => $request->user()->id,
            'status' => $data['status'],
            'problem_resolved' => $data['problem_resolved'] ?? false,
            'unresolved_reason' => $data['unresolved_reason'] ?? null,
            'comment' => $data['comment'] ?? null,
        ]);

        $intervention->load(['site', 'user', 'media']);

        return response()->json($intervention, 201);
    }

    public function show(Intervention $intervention)
    {
        $user = request()->user();
        if ($user->role !== User::ROLE_MANAGER && $intervention->user_id !== $user->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $intervention->load(['site', 'user', 'media']);

        return response()->json($intervention);
    }

    public function update(Request $request, Intervention $intervention)
    {
        $user = $request->user();
        if ($user->role !== User::ROLE_MANAGER && $intervention->user_id !== $user->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $data = $request->validate([
            'status' => ['sometimes', 'string'],
            'problem_resolved' => ['sometimes', 'boolean'],
            'unresolved_reason' => ['nullable', 'string', 'max:255'],
            'comment' => ['nullable', 'string'],
        ]);

        $intervention->fill($data);
        $intervention->save();
        $intervention->load(['site', 'user', 'media']);

        return response()->json($intervention);
    }
}
