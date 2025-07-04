<?php

namespace App\Http\Controllers;

use App\Models\Avance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Notifications\AvanceValidee;

class AvanceController extends Controller
{
    public function index()
    {
        Log::info('✅ Test log depuis AvanceController@index');

        $avances = Auth::user()->is_admin
            ? Avance::with('user')->latest()->get()
            : Avance::where('user_id', Auth::id())->latest()->get();

        return view('avances.index', compact('avances'));
    }

    public function create()
    {
        // 1) Période du mois courant
        $start  = Carbon::now()->startOfMonth();
        $end    = Carbon::now()->endOfMonth();
        $period = CarbonPeriod::create($start, $end);

        // 2) Jours ouvrés (lundi à vendredi)
        $weekdays = collect($period)
            ->filter(fn(Carbon $d) => $d->isWeekday());

        // 3) Dates d’absences ce mois-ci
        $absences = Auth::user()->absences()
            ->whereMonth('date', Carbon::now()->month)
            ->pluck('date')
            ->map(fn($d) => Carbon::parse($d)->toDateString());

        // 4) Nombre de jours réellement travaillés
        $joursTravailles = $weekdays
            ->reject(fn(Carbon $d) => $absences->contains($d->toDateString()))
            ->count();

        // 5) Calculs brut & net
        $brut = $joursTravailles * 7 * 11.65;          // SMIC * 7h/j
        $net  = round($brut * 0.77, 2);               // -23%

        // 6) Préparation du calendrier
        $calendarDays = collect($period)
            ->map(fn(Carbon $d) => [
                'day'    => $d->day,
                'date'   => $d->toDateString(),
                'status' => $absences->contains($d->toDateString())
                            ? 'absent'
                            : ($d->isWeekday() && $d->lte(Carbon::today())
                                ? 'worked'
                                : 'future'),
            ])
            ->toArray();

        return view('avances.create', compact(
            'joursTravailles',
            'brut',
            'net',
            'calendarDays'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'jours_travailles' => 'required|integer|min:1|max:31',
        ]);

        $brut = $request->jours_travailles * 7 * 11.65;
        $net  = round($brut * 0.77, 2);

        Avance::create([
            'user_id'          => Auth::id(),
            'jours_travailles' => $request->jours_travailles,
            'montant_brut'     => $brut,
            'montant_net'      => $net,
            'statut'           => 'en_attente',
        ]);

        return redirect()->route('avances.index')
                         ->with('success', 'Demande enregistrée.');
    }

    public function valider(Avance $avance)
    {
        try {
            Log::info("🔄 Validation en cours pour avance ID {$avance->id}");

            $avance->update(['statut' => 'validée']);

            if ($user = $avance->user) {
                Log::info("📤 Tentative d’envoi de notification à {$user->email}");
                $user->notify(new AvanceValidee($avance));
                Log::info("✅ Notification envoyée à {$user->email}");
            } else {
                Log::warning("⚠️ Utilisateur non trouvé pour l’avance ID {$avance->id}");
            }

            return redirect()->route('avances.index')
                             ->with('success', 'Avance validée et notification envoyée.');
        } catch (\Throwable $e) {
            Log::error("❌ Erreur notification avance : " . $e->getMessage());
            return back()->withErrors('Erreur lors de l’envoi de la notification.');
        }
    }
}
