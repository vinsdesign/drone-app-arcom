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
}
