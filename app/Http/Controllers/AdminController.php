<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Enterprise;
use App\Models\User;

class AdminController extends Controller
{
    /**
     * Display the admin dashboard with some enterprise statistics.
     */
    public function index()
    {
        $user = auth()->user();
        $enterprise = $user->enterprise;

        $employeeCount = $enterprise ? $enterprise->employees()->count() : 0;
        $pendingAbsences = $enterprise
            ? $enterprise->absences()->where('statut', 'en attente')->count()
            : 0;

        return view('admin.dashboard', [
            'enterprise' => $enterprise,
            'employeeCount' => $employeeCount,
            'pendingAbsences' => $pendingAbsences,
        ]);
    }

    /**
     * Display a list of all users in the current enterprise.
     */
    public function users()
    {
        $user = auth()->user();
        $enterprise = $user->enterprise;
        $users = $enterprise ? $enterprise->users()->latest()->get() : collect();

        return view('admin.users', compact('users'));
    }

    /**
     * Promote an employee to admin role.
     */
    public function promote(User $user)
    {
        $user->update(['role' => 'admin']);

        return redirect()->route('admin.users')
            ->with('success', 'Utilisateur promu administrateur.');
    }
}
