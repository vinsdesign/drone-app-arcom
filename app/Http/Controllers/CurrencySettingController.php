<?php
namespace App\Http\Controllers;

use App\Models\currencie;
use App\Models\maintence_drone;
use App\Models\maintence_eq;
use App\Models\Projects;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Http\Request;
use App\Models\team;

class CurrencySettingController extends Controller
{
    public function showCurrencyForm()
    {
        $currencies = currencie::all(); // Ambil semua mata uang dari database
        
        return view('filament\pages\currency-settings', compact('currencies')); // Gunakan nama file view yang tepat
    }

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'currency_id' => 'required|exists:currencies,id',
    //     ]);

    //     $team = auth()->user()->teams()->first()->id; 
    //     $team->currency_id = $request->currency_id; 
    //     $team->save();

    //     return redirect()->route('settings')->with('success', 'Default currency updated successfully.');
    // }
    public function store(Request $request)
{
    $request->validate([
        'currency_id' => 'required|exists:currencies,id',
    ]);

    // Mengambil ID tim berdasarkan pengguna yang sedang login
    $currentTeam = auth()->user()->teams()->first();
    // $selectedCurrencyId = $currentTeam ? $currentTeam->currencies_id : null;

    if ($currentTeam) { 
        // Mengupdate currency_id pada tim
        $currentTeam->currencies_id = $request->currency_id; 
        $currentTeam->save();

        // Mendapatkan ID tenant d // Pastikan Anda memiliki relasi tenant_id yang benar
        // dd($currentTeam);
        if ($request->has('set_as_default')) {
            Projects::where('teams_id', $currentTeam->id)->update(['currencies_id' => $request->currency_id]);
            maintence_eq::where('teams_id', $currentTeam->id)->update(['currencies_id' => $request->currency_id]);
            maintence_drone::where('teams_id', $currentTeam->id)->update(['currencies_id' => $request->currency_id]);
        }
        return redirect()->route('filament.admin.resources.settings.index', ['tenant' => $currentTeam->id])
                         ->with('success', 'Default currency updated successfully.');
    } else {
        return redirect()->route('currency-settings', ['tenant' => $currentTeam->id])->with('error', 'Team not found.');
    }
}
}
