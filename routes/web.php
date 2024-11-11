<?php

use App\Filament\Pages\Report;
use App\Filament\Pages\Settings;
use App\Http\Controllers\buttonPopUpCreate;
use App\Http\Controllers\createProject;
use App\Http\Controllers\importDefaultValue;
use Illuminate\Support\Facades\Route;
use Filament\Http\Livewire\Auth\Login;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CurrencySettingController;
use App\Livewire\DroneStatistik;
use App\Livewire\BatteryStatistik;
use App\Http\Controllers\LanguageController;

Route::get('/', function () {
    return redirect('/admin');
});
Route::post('/send-email', [ContactController::class, 'sendEmail'])->name('sendEmail');

Route::get('/report', Report::class)->name('filament.report');
Route::post('/report/download', [Report::class, 'downloadReport'])->name('filament.report.download');
Route::post('/filament/report/inventory/download', [Report::class, 'downloadInventoryReport'])->name('filament.report.inventory.download');
Route::post('/filament/report/incomeExpense/download', [Report::class, 'downloadIncomeExpenseReport'])->name('filament.report.incomeExpense.download');
Route::get('/drone-statistik/{drone_id}', [DroneStatistik::class, 'showDroneStatistik'])->name('drone.statistik');
Route::get('/battery-statistik/{battery_id}', [BatteryStatistik::class, 'showBatteryStatistik'])->name('battery.statistik');

// Route::get('admin/{tenant}/settings', [CurrencySettingController::class, 'index'])->name('settings');
Route::get('admin/{tenant}/settings/currency-settings', [CurrencySettingController::class, 'showCurrencyForm'])->name('currency-settings');
Route::post('/currency-settings/store', [CurrencySettingController::class, 'store'])->name('currency-store');
Route::post('/default-value',[importDefaultValue::class,'store'])->name('default-value');

//project
Route::get('/flight-project/{project_id}', function(){
    return view('component.flight-project');
})->name('flight-peroject');
Route::post('/create-project',[buttonPopUpCreate::class,'buttonProject'])->name('create-project');
Route::post('/create-customer',[buttonPopUpCreate::class,'buttonDrone'])->name('create-drone');
Route::post('/create-battrei',[buttonPopUpCreate::class,'buttonBattrei'])->name('create-battrei');
Route::post('/create-equipment',[buttonPopUpCreate::class,'buttonEquipment'])->name('create-equipment');
Route::post('/create-location',[buttonPopUpCreate::class,'buttonLocation'])->name('create-Location');

//language change
Route::post('/change-language', [LanguageController::class, 'changeLanguage'])->name('change.language');

