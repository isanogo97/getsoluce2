<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Absence;
use Illuminate\Support\Facades\Auth;

class AbsenceController extends Controller
{
    public function index()
    {
        $absences = Auth::user()->is_admin
            ? Absence::with('user')->latest()->get()
            : Absence::where('user_id', Auth::id())->latest()->get();

        return view('absences.index', compact('absences'));
    }

    public function create()
    {
        return view('absences.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'date'  => 'required|date',
            'motif' => 'required|string|max:255',
        ]);

        Absence::create([
            'user_id' => Auth::id(),
            'date'    => $request->date,
            'motif'   => $request->motif,
            'statut'  => 'en attente',
        ]);

        return redirect()->route('absences.index')
                         ->with('success', 'Demande d\'absence enregistrée.');
    }

    public function edit(Absence $absence)
    {
        $this->authorize('update', $absence);
        return view('absences.edit', compact('absence'));
    }

    public function update(Request $request, Absence $absence)
    {
        $this->authorize('update', $absence);

        $request->validate([
            'date'  => 'required|date',
            'motif' => 'required|string|max:255',
        ]);

        $absence->update($request->only('date', 'motif'));

        return redirect()->route('absences.index')
                         ->with('success', 'Absence mise à jour.');
    }

    public function destroy(Absence $absence)
    {
        $this->authorize('delete', $absence);
        $absence->delete();

        return back()->with('success', 'Absence supprimée.');
    }

    // Admin only: valider
    public function validateAbsence(Absence $absence)
    {
        $this->authorize('validate', $absence);
        $absence->update(['statut' => 'validé']);
        return back()->with('success', 'Absence validée.');
    }

    // Admin only: refuser
    public function rejectAbsence(Absence $absence)
    {
        $this->authorize('validate', $absence);
        $absence->update(['statut' => 'refusé']);
        return back()->with('success', 'Absence refusée.');
    }
}
