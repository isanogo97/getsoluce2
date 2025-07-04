<?php

namespace App\Http\Controllers;

use App\Models\Compte;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompteController extends Controller
{
    public function index()
    {
        $comptes = Auth::user()->is_admin
            ? Compte::with('user')->latest()->get()
            : Compte::where('user_id', Auth::id())->latest()->get();

        return view('comptes.index', compact('comptes'));
    }

    public function create()
    {
        return view('comptes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'type' => 'required|in:courant,épargne',
            'solde' => 'required|numeric|min:0',
        ]);

        Compte::create([
            'user_id' => Auth::id(),
            'nom' => $request->nom,
            'type' => $request->type,
            'solde' => $request->solde,
        ]);

        return redirect()->route('comptes.index')->with('success', 'Compte créé.');
    }

    public function edit(Compte $compte)
    {
        $this->authorize('update', $compte);
        return view('comptes.edit', compact('compte'));
    }

    public function update(Request $request, Compte $compte)
    {
        $this->authorize('update', $compte);

        $request->validate([
            'nom' => 'required|string|max:255',
            'type' => 'required|in:courant,épargne',
            'solde' => 'required|numeric|min:0',
        ]);

        $compte->update([
            'nom' => $request->nom,
            'type' => $request->type,
            'solde' => $request->solde,
        ]);

        return redirect()->route('comptes.index')->with('success', 'Compte mis à jour.');
    }

    public function destroy(Compte $compte)
    {
        $this->authorize('delete', $compte);
        $compte->delete();

        return redirect()->route('comptes.index')->with('success', 'Compte supprimé.');
    }
}
