<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function changeLanguage(Request $request)
    {
        // session(['locale' => $request->input('language')]);
        // return redirect()->back();

        $locale = $request->input('language');
        session(['locale' => $locale]);
        session(['locale_timestamp' => now()->timestamp]);
        return redirect()->back();
    }

}
