<?php

use App\Filament\Pages\Report;
use App\Filament\Pages\Settings;
use App\Http\Controllers\buttonPopUpCreate;
use App\Http\Controllers\createProject;
use App\Http\Controllers\importDefaultValue;
use App\Http\Controllers\popUpViewResource;
use App\Http\Controllers\setYear;
use App\Livewire\EquipmentStatistik;
use Illuminate\Support\Facades\Route;
use Filament\Http\Livewire\Auth\Login;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CurrencySettingController;
use App\Livewire\DroneStatistik;
use App\Livewire\BatteryStatistik;
use App\Livewire\LocationStatistik;
use App\Http\Controllers\LanguageController;

Route::get('/', function () {
    return redirect('/admin');
});
Route::post('/send-email', [ContactController::class, 'sendEmail'])->name('sendEmail');

Route::get('/report', Report::class)->name('filament.report');
Route::post('/report/download', [Report::class, 'downloadReport'])->name('filament.report.download');
Route::post('/filament/report/inventory/download', [Report::class, 'downloadInventoryReport'])->name('filament.report.inventory.download');
Route::post('/filament/report/incomeExpense/download', [Report::class, 'downloadIncomeExpenseReport'])->name('filament.report.incomeExpense.download');
//infolist statistik widget
Route::get('/drone-statistik/{drone_id}', [DroneStatistik::class, 'showDroneStatistik'])->name('drone.statistik');
Route::get('/battery-statistik/{battery_id}', [BatteryStatistik::class, 'showBatteryStatistik'])->name('battery.statistik');
Route::get('/equipment-statistik/{equipment_id}', [EquipmentStatistik::class, 'showEquipmentStatistik'])->name('equipment.statistik');
Route::get('/location-statistik/{location_id}', [LocationStatistik::class, 'showLocationStatistik'])->name('location.statistik');

// Route::get('admin/{tenant}/settings', [CurrencySettingController::class, 'index'])->name('settings');
Route::get('admin/{tenant}/settings/currency-settings', [CurrencySettingController::class, 'showCurrencyForm'])->name('currency-settings');
Route::post('/currency-settings/store', [CurrencySettingController::class, 'store'])->name('currency-store');
Route::post('/default-value',[importDefaultValue::class,'store'])->name('default-value');

//project-flight
Route::get('/flight-project/{project_id}', function(){
    return view('component.flight-project');
})->name('flight-peroject');
//personnel-flight
Route::get('/flight-personnel/{personnel_id}', function(){
    return view('component.flight-personnel');
})->name('flight-personnel');
//location flight
Route::get('/flight-location/{location_id}', function(){
    return view('component.flight-location');
})->name('flight-location');
//pop-up button flight create
Route::post('/create-project',[buttonPopUpCreate::class,'buttonProject'])->name('create-project');
Route::post('/create-customer',[buttonPopUpCreate::class,'buttonDrone'])->name('create-drone');
Route::post('/create-battrei',[buttonPopUpCreate::class,'buttonBattrei'])->name('create-battrei');
Route::post('/create-equipment',[buttonPopUpCreate::class,'buttonEquipment'])->name('create-equipment');
Route::post('/create-location',[buttonPopUpCreate::class,'buttonLocation'])->name('create-Location');

//language change
Route::post('/change-language', [LanguageController::class, 'changeLanguage'])->name('change.language');
//chart js
//year setting
Route::post('/change-year', [setYear::class, 'changeYear'])->name('change.year');

//popUp View Resources
Route::post('/create-document-project',[popUpViewResource::class,'createProjectDocument'])->name('create.document.project');
Route::post('/create-document-flight',[popUpViewResource::class,'createFlightDocument'])->name('create.document.flight');
Route::post('/create-media-flight',[popUpViewResource::class,'createMediaFlight'])->name('create.media.flight');
Route::post('/create-document-equipment',[popUpViewResource::class,'createEquipmentDocument'])->name('create.document.equipment');
Route::post('/create-document-battrei',[popUpViewResource::class,'createBattreiDocument'])->name('create.document.battrei');
Route::post('/create-document-drone',[popUpViewResource::class,'createDroneDocument'])->name('create.document.drone');
Route::post('/create-document-personnel',[popUpViewResource::class,'createPersonnelDocument'])->name('create.document.personnel');
Route::post('/edit-media-flight',[popUpViewResource::class,'buttonValues'])->name('edit.media.flight');
Route::post('/create-media-flight-record',[popUpViewResource::class,'createMediaFlightRecord'])->name('create.media.flight.record');
