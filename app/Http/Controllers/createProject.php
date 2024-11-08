<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;

class createProject extends Controller
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
        $teamId = Auth()->user()->teams()->first()->id;
            $result = DB::table('projects')
                ->create([
                    'teams_id' => $teamId,
                    'case'=> $request['case'],
                    'revenue'=> $request['revenue'],
                    'currency'=> $request['currencies_id'],
                    'description'=> $request['description'],
                    'customer_id'=> $request['customers_id'],
                ]);

            if ($result) {
                return redirect()->back()
                    ->with('success', 'Updated successfully.');
            } else {
                return redirect()->back()
                    ->with('error', 'Update failed. Team not found.');
            }
    }
}
