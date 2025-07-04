<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Absence;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Comptage des absences
        $absencesCount = Auth::user()->is_admin
            ? Absence::whereHas('user', fn($q) => $q->where('enterprise_id', Auth::user()->enterprise_id))
                ->count()
            : Absence::where('user_id', Auth::id())
                ->count();

        // Dernières absences validées
        $allValidAbsences = Auth::user()->is_admin
            ? Absence::with('user')
                ->where('statut', 'validé')
                ->whereHas('user', fn($q) => $q->where('enterprise_id', Auth::user()->enterprise_id))
                ->orderBy('date','desc')
                ->limit(10)
                ->get()
            : Absence::with('user')
                ->where('statut', 'validé')
                ->where('user_id', Auth::id())
                ->orderBy('date','desc')
                ->limit(10)
                ->get();

        // Anniversaires du jour (dans la même entreprise pour l'admin)
        $today = Carbon::today();
        $birthdays = Auth::user()->is_admin
            ? User::where('enterprise_id', Auth::user()->enterprise_id)
                ->whereMonth('date_naissance', $today->month)
                ->whereDay('date_naissance', $today->day)
                ->get()
            : User::where('id', Auth::id())
                ->whereMonth('date_naissance', $today->month)
                ->whereDay('date_naissance', $today->day)
                ->get();

        return view('dashboard', compact('absencesCount', 'allValidAbsences', 'birthdays'));
    }
}
