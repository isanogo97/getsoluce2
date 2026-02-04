<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Site;
use App\Services\SpreadsheetImporter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SiteController extends Controller
{
    public function index(Request $request)
    {
        $query = Site::query();

        if ($request->filled('site_number')) {
            $query->where('site_number', 'like', '%'.$request->string('site_number').'%');
        }

        if ($request->filled('filter')) {
            $filter = $request->string('filter')->toString();
            if ($filter === 'in_progress') {
                $query->whereHas('interventions', function ($subQuery) {
                    $subQuery->whereIn('status', ['en_route', 'sur_place']);
                });
            }

            if ($filter === 'pending') {
                $query->whereHas('interventions', function ($subQuery) {
                    $subQuery->where('status', 'en_attente');
                });
            }

            if ($filter === 'unresolved') {
                $query->whereHas('interventions', function ($subQuery) {
                    $subQuery->where('problem_resolved', false);
                });
            }
        }

        $sites = $query
            ->with(['interventions' => fn ($relation) => $relation->latest()->limit(5)])
            ->orderBy('site_number')
            ->paginate(25);

        return response()->json($sites);
    }

    public function show(Site $site)
    {
        $site->load(['interventions.user', 'interventions.media']);

        return response()->json($site);
    }

    public function import(Request $request, SpreadsheetImporter $importer)
    {
        $request->validate([
            'file' => ['required', 'file'],
        ]);

        $rows = $importer->fromUploadedFile($request->file('file'));
        $created = 0;
        $updated = 0;
        $errors = [];

        foreach ($rows as $index => $row) {
            $normalized = [
                'site_number' => $row['site_number'] ?? $row['numero_site'] ?? $row['num_site'] ?? null,
                'name' => $row['name'] ?? $row['nom'] ?? null,
                'address' => $row['address'] ?? $row['adresse'] ?? null,
                'comment' => $row['comment'] ?? $row['commentaire'] ?? null,
            ];

            $validator = Validator::make($normalized, [
                'site_number' => ['required', 'string'],
                'name' => ['required', 'string'],
                'address' => ['required', 'string'],
                'comment' => ['nullable', 'string'],
            ]);

            if ($validator->fails()) {
                $errors[] = [
                    'row' => $index + 2,
                    'errors' => $validator->errors()->all(),
                ];
                continue;
            }

            $site = Site::updateOrCreate(
                ['site_number' => $normalized['site_number']],
                $normalized
            );

            if ($site->wasRecentlyCreated) {
                $created++;
            } else {
                $updated++;
            }
        }

        return response()->json([
            'created' => $created,
            'updated' => $updated,
            'errors' => $errors,
        ]);
    }
}
