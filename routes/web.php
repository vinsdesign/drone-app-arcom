<?php

use Illuminate\Support\Facades\Route;
use Filament\Http\Livewire\Auth\Login;
use App\Http\Controllers\ContactController;

Route::get('/', function () {
    return redirect('/admin');
});
Route::post('/send-email', [ContactController::class, 'sendEmail'])->name('sendEmail');
