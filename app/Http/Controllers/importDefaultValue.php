<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class importDefaultValue extends Controller
{
    public function store(Request $request)
    {
        // Validasi data yang masuk
        // $validatedData = $request->validate([
        //     'customer' => 'exists:customers,id',
        //     'projects' => 'exists:projects,id',
        //     'flight_type' => 'string',
        //     'pilot' => 'boolean',
        // ]);
        // dd($validatedData);
        if($request['pilot']== "on"){
            $pilot=1;
        }else{
            $pilot=0;
        }

        $teamId = Auth()->user()->teams()->first()->id;
            $result = DB::table('teams')
                ->where('id', $teamId)
                ->update([
                    'id_customers' => $request['customer'],
                    'id_projects' => $request['projects'],
                    'flight_type' => $request['flight_type'],
                    'set_pilot' => $pilot,
                ]);

            if ($result) {
                return redirect()->route('filament.admin.resources.settings.index', ['tenant' => $teamId])
                    ->with('success', 'Updated successfully.');
            } else {
                return redirect()->route('filament.admin.resources.settings.index', ['tenant' => $teamId])
                    ->with('error', 'Update failed. Team not found.');
            }
    }
}
