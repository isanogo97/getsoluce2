<?php

namespace App\Http\Controllers;

use App\Models\Conge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CongeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $conges = Auth::user()->is_admin
            ? Conge::latest()->get()
            : Conge::where('user_id', Auth::id())->latest()->get();

        return view('conges.index', compact('conges'));
    }

    public function create()
    {
        return view('conges.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'date_debut' => 'required|date|after_or_equal:today',
            'date_fin'   => 'required|date|after_or_equal:date_debut',
            'motif'      => 'required|string|max:255',
        ]);

        Conge::create([
            'user_id'    => Auth::id(),
            'date_debut' => $request->date_debut,
            'date_fin'   => $request->date_fin,
            'motif'      => $request->motif,
            'statut'     => 'en_attente',
        ]);

        return redirect()->route('conges.index')
                         ->with('success', 'Votre demande de congé a bien été enregistrée.');
    }

    public function edit(Conge $conge)
    {
        $this->authorize('update', $conge);

        return view('conges.edit', compact('conge'));
    }

    public function update(Request $request, Conge $conge)
    {
        $this->authorize('update', $conge);

        $request->validate([
            'date_debut' => 'required|date|after_or_equal:today',
            'date_fin'   => 'required|date|after_or_equal:date_debut',
            'motif'      => 'required|string|max:255',
        ]);

        $conge->update([
            'date_debut' => $request->date_debut,
            'date_fin'   => $request->date_fin,
            'motif'      => $request->motif,
        ]);

        return redirect()->route('conges.index')
                         ->with('success', 'Votre congé a bien été modifié.');
    }

    public function destroy(Conge $conge)
    {
        $this->authorize('delete', $conge);

        $conge->delete();

        return redirect()->route('conges.index')
                         ->with('success', 'Votre congé a bien été supprimé.');
    }

    public function valider(Conge $conge)
    {
        $this->authorize('admin'); // sécurité

        $conge->statut = 'validé';
        $conge->save();

        // Optionnel : Notification à l'utilisateur
        // Notification::send($conge->user, new CongeValideNotification($conge));

        return redirect()->route('conges.index')
                         ->with('success', 'Le congé a été validé.');
    }
}
