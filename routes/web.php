<?php

use App\Filament\Pages\Report;
use Illuminate\Support\Facades\Route;
use Filament\Http\Livewire\Auth\Login;

Route::get('/', function () {
    return redirect('/admin');
});

Route::get('/report', Report::class)->name('filament.report');
Route::post('/report/download', [Report::class, 'downloadReport'])->name('filament.report.download');
