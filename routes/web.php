<?php

use App\Filament\Pages\Report;
use Illuminate\Support\Facades\Route;
use Filament\Http\Livewire\Auth\Login;
use App\Http\Controllers\ContactController;

Route::get('/', function () {
    return redirect('/admin');
});
Route::post('/send-email', [ContactController::class, 'sendEmail'])->name('sendEmail');

Route::get('/report', Report::class)->name('filament.report');
Route::post('/report/download', [Report::class, 'downloadReport'])->name('filament.report.download');

