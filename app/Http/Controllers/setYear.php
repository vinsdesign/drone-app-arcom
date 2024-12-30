<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class setYear extends Controller
{
    public function changeYear(Request $request)
    {
        $tahun = $request->input('tahun');

        // session()->forget('tahun');

        session()->put('tahun', $tahun);

        return response()->json(['tahun' => $tahun]);
    }
    public function setSessionProject(Request $request){
        $data = $request->input('dataSession');
        session()->put('project_id', $data);
        return response()->json(['redirect' => route('flight-peroject',['project_id' => $data])]);
    }
    public function setSessionLocation(Request $request){
        $data = $request->input('dataSession');
        session()->put('location_id', $data);
        return response()->json(['redirect' => route('flight-location', ['location_id' => $data])]);
    }
    public function setSessionPersonnel(Request $request){
        $data = $request->input('dataSession');
        session()->put('personnel_id', $data);
        return response()->json(['redirect' => route('flight-personnel', ['personnel_id' => $data])]);
    }
}
