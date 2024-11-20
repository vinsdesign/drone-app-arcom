<?php

namespace App\Livewire;

use App\Models\equidment;
use DB;
use Filament\Widgets\ChartWidget;
use App\Models\fligh;
use Filament\Widgets\Widget;
use Illuminate\Support\Carbon;
use App\Helpers\TranslationHelper;

class EquipmentStatistik extends Widget
{
    protected static ?string $heading = 'Equipment Activity Statistics';
    protected int|string|array $columnSpan = 'full';
    protected static bool $isLazy = false;
    public $equipment_id;
    public $equipment;
    protected $listeners = ['showEquipmentStatistik'];
    protected static string $view = 'component.chartjs.equipment-statistik';

    public function showEquipmentStatistik($id)
    {
    // dd($this->Drone = $id);
    $this->equipment_id = $id;
    $this->equipment = equidment::find($id);
    session(['equipment_id' => $id]);
    // $this->emitSelf('dataUpdated');
    if (request()->routeIs('filament.admin.resources.equidments.view')) {
        return; 
    }return redirect()->route('filament.admin.resources.equidments.view', [
        'tenant' => Auth()->user()->teams()->first()->id,
        'record' => $id
    ]);

}
//     protected function getData(): array
//     {
//         $tenant_id = Auth()->user()->teams()->first()->id;
//         $equipmentID = session('equipment_id');

//         //ulang baru
//         //end
//         $pivotEquipment_id = DB::table('fligh_equidment')->where('equidment_id', $equipmentID)
//         ->select('fligh_id')
//         ->distinct() // Mengambil id unik dari penerbangan
//         ->pluck('fligh_id');
    
//     // Step 2: Ambil data penerbangan dari tabel `fligh`
//     $flights = fligh::where('teams_id', $tenant_id)
//         ->whereIn('id', $pivotEquipment_id)
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
//         $equipmentID = session('equipment_id');
//         $tenant_id = Auth()->user()->teams()->first()->id;
//         $pivotEquipment = DB::table('fligh_equidment')->where('equidment_id', $equipmentID)
//         ->select('fligh_id')
//         ->distinct()
//         ->pluck('fligh_id');
//         $flights = fligh::where('teams_id', $tenant_id)
//         ->whereIn('id', $pivotEquipment)
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
