<?php

namespace App\Http\Controllers;

use App\Models\NoteDeFrais;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class NoteDeFraisController extends Controller
{
    public function index()
    {
        $notes = Auth::user()->is_admin
            ? NoteDeFrais::with('user')->latest()->get()
            : NoteDeFrais::where('user_id', Auth::id())->latest()->get();

        return view('notedefrais.index', compact('notes'));
    }

    public function create()
    {
        return view('notedefrais.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date'         => 'required|date',
            'montant'      => 'required|numeric|min:0',
            'description'  => 'nullable|string|max:255',
            'justificatif' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        if (!$request->hasFile('justificatif')) {
            return back()->withErrors(['justificatif' => 'Le justificatif est requis.']);
        }

        $path = $request->file('justificatif')->store('justificatifs', 'public');

        NoteDeFrais::create([
            'user_id'      => Auth::id(),
            'date'         => $validated['date'],
            'montant'      => $validated['montant'],
            'description'  => $validated['description'],
            'justificatif' => $path,
            'statut'       => 'en_attente',
        ]);

        return redirect()->route('notedefrais.index')->with('success', 'Note de frais enregistrée.');
    }

    public function edit(NoteDeFrais $notedefrai)
    {
        $this->authorize('update', $notedefrai);
        return view('notedefrais.edit', ['notedefrai' => $notedefrai]);
    }

    public function update(Request $request, NoteDeFrais $notedefrai)
    {
        $this->authorize('update', $notedefrai);

        $validated = $request->validate([
            'date'         => 'required|date',
            'montant'      => 'required|numeric|min:0',
            'description'  => 'nullable|string|max:255',
            'justificatif' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('justificatif')) {
            Storage::disk('public')->delete($notedefrai->justificatif);
            $path = $request->file('justificatif')->store('justificatifs', 'public');
            $notedefrai->justificatif = $path;
        }

        $notedefrai->update([
            'date'        => $validated['date'],
            'montant'     => $validated['montant'],
            'description' => $validated['description'],
        ]);

        return redirect()->route('notedefrais.index')->with('success', 'Note de frais mise à jour.');
    }

    public function destroy(NoteDeFrais $notedefrai)
    {
        $this->authorize('delete', $notedefrai);

        Storage::disk('public')->delete($notedefrai->justificatif);
        $notedefrai->delete();

        return redirect()->route('notedefrais.index')->with('success', 'Note de frais supprimée.');
    }

    public function valider(NoteDeFrais $note)
    {
        $note->statut = 'Accepté';
        $note->save();

        return redirect()->route('notedefrais.index')->with('success', 'Note de frais validée.');
    }
}
