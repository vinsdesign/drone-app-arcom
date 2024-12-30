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
        session('project_id', $data);
    }
    public function setSessionLocation(Request $request){
        $data = $request->input('dataSession');
        session('location_id', $data);
    }
    public function setSessionPersonnel(Request $request){
        $data = $request->input('dataSession');
        session('personnel_id', $data);
    }
}
