<?php

namespace App\Livewire;

use App\Models\battrei;
use DB;
use Filament\Widgets\ChartWidget;
use App\Models\fligh;
use Filament\Widgets\Widget;
use Illuminate\Support\Carbon;
use App\Helpers\TranslationHelper;

class BatteryStatistik extends Widget
{
    protected static ?string $heading = 'Battery Activity Statistics';
    protected int|string|array $columnSpan = 'full';
    protected static bool $isLazy = false;
    public $battery_id;
    public $battery;
    protected $listeners = ['showBatteryStatistik'];
    protected static string $view = 'component.chartjs.batterei-statisik';

    public function showBatteryStatistik($id)
    {
    // dd($this->Drone = $id);
    $this->battery_id = $id;
    $this->battery = battrei::find($id);
    session(['battery_id' => $id]);
    // $this->emitSelf('dataUpdated');
    if (request()->routeIs('filament.admin.resources.batteris.view')) {
        return; 
    }return redirect()->route('filament.admin.resources.battreis.view', [
        'tenant' => Auth()->user()->teams()->first()->id,
        'record' => $id
    ]);

}
//     protected function getData(): array
//     {
//         $tenant_id = Auth()->user()->teams()->first()->id;
//         $batteryID = session('battery_id');

//         //ulang baru
//         //end
//         $pivotBattrei = DB::table('fligh_battrei')->where('battrei_id', $batteryID)
//         ->select('fligh_id') // Pastikan nama kolom id penerbangan benar
//         ->distinct() // Mengambil id unik dari penerbangan
//         ->pluck('fligh_id');
    
//     // Step 2: Ambil data penerbangan dari tabel `fligh`
//     $flights = fligh::where('teams_id', $tenant_id)
//         ->whereIn('id', $pivotBattrei)
//         ->whereBetween('start_date_flight', [now()->startOfYear(), now()->endOfYear()])
//         ->get();
    
//     // Step 3 & 4: Kelompokkan data berdasarkan tanggal dan hitung total penerbangan dan durasi
//     $data = $flights->groupBy(function ($item) {
//         return Carbon::parse($item->start_date_flight)->format('Y-m');
//     })->map(function ($group) {
//         // Hitung total penerbangan
//         $totalFlights = $group->count();
    
//         // Hitung total durasi (dalam detik) dan konversi ke jam
//         $totalDuration = $group->sum(function ($flight) {
//             list($hours, $minutes, $seconds) = explode(':', $flight->duration);
//             return ($hours * 3600) + ($minutes * 60) + $seconds;
//         });
//         $totalHours = $totalDuration / 3600;
//         $formatTotalHour = round($totalHours, 1);
    
//         return [
//             'totalFlights' => $totalFlights,
//             'totalDuration' => $formatTotalHour,
//         ];
//     });
    
//         return [
//             'datasets' => [
//                 [
//                     'label' => (TranslationHelper::translateIfNeeded('Flights Total ')),
//                     'data' => $data->pluck('totalFlights')->values()->toArray(),
//                     'backgroundColor' => 'rgba(75, 192, 192, 0.6)',
//                 ],
//                 [
//                     'label' => (TranslationHelper::translateIfNeeded('Flight Duration (Hours)')),
//                     'data' => $data->pluck('totalDuration')->values()->toArray(),
//                     'backgroundColor' => 'rgba(255, 99, 132, 0.6)',
//                 ],
//             ],
//             'labels' => $data->keys()->toArray(),
//         ];
//     }
//     protected function getOptions(): array
//     {
//         $batteryID = session('battery_id');
//         $tenant_id = Auth()->user()->teams()->first()->id;
//         $pivotBattrei = DB::table('fligh_battrei')->where('battrei_id', $batteryID)
//         ->select('fligh_id')
//         ->distinct()
//         ->pluck('fligh_id');
//     $flights = fligh::where('teams_id', $tenant_id)
//         ->whereIn('id', $pivotBattrei)
//         ->whereBetween('start_date_flight', [now()->startOfYear(), now()->endOfYear()])
//         ->get();
//             $data = $flights->groupBy(function ($item) {
//                 return Carbon::parse($item->start_date_flight)->format('Y-m'); // Grup per bulan
//             })->map(function ($group) {
//                 $totalFlights = $group->count();
//                 $totalDurationInSeconds = $group->sum(function ($flight) {
//                     list($hours, $minutes, $seconds) = explode(':', $flight->duration);
//                     return ($hours * 3600) + ($minutes * 60) + $seconds;
//                 });
//                 $totalHours = floor($totalDurationInSeconds / 3600);
//                 $totalMinutes = floor(($totalDurationInSeconds % 3600) / 60);
//                 $totalSeconds = $totalDurationInSeconds % 60;
            
//                 return [
//                     'totalFlights' => $totalFlights,
//                     'totalDuration' => sprintf('%02d:%02d:%02d', $totalHours, $totalMinutes, $totalSeconds), 
//                 ];
//             });
        
//             $totalFlightsPerMonth = $data->pluck('totalFlights')->sum();
//             $totalDurationInSeconds = $flights->sum(function ($flight) {
//                 list($hours, $minutes, $seconds) = explode(':', $flight->duration);
//                 return ($hours * 3600) + ($minutes * 60) + $seconds;
//             });
            
//             $totalHours = floor($totalDurationInSeconds / 3600);
//             $totalMinutes = floor(($totalDurationInSeconds % 3600) / 60);
//             $totalSeconds = $totalDurationInSeconds % 60;
//             // Format total durasi
//             $formattedTotalDuration = sprintf('%02d:%02d:%02d', $totalHours, $totalMinutes, $totalSeconds);    
//         return [
//             'plugins' => [
//                 'title' => [
//                     'display' => true,
//                     'text' => (TranslationHelper::translateIfNeeded('Flights Total: ')) . $totalFlightsPerMonth . (TranslationHelper::translateIfNeeded(' | Duration Total: ')) . $formattedTotalDuration,
//                     'font' => [
//                         'size' => 16,
//                     ],
//                 ],
//             ],
//         ];
        
//     }
//     public function getDescription(): ?string
// {
//     return (TranslationHelper::translateIfNeeded('Total flights and monthly duration'));
// }
//     protected function getType(): string
//     {
//         return 'bar';
//     }
}
