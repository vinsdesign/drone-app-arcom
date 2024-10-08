<?php

use Illuminate\Support\Facades\Route;
use Filament\Http\Livewire\Auth\Login;

Route::get('/', function () {
    return redirect('/admin');
});
