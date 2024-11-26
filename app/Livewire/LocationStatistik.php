<?php

namespace App\Livewire;

use App\Models\fligh;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;
use App\Models\fligh_location;
use Filament\Widgets\Widget;
use Livewire\Component;
use Illuminate\Contracts\View\View;
use Route;

class LocationStatistik extends Widget
{
    protected static bool $isLazy = false;
    protected static ?string $heading = 'Location Activity Statistics';
    protected int|string|array $columnSpan = 'full';
    public $location_id;
    public $fligh_location;
    protected $listeners = ['showLocationStatistik'];
    protected static string $view = "component.chartjs.location-statistik";

    public function showLocationStatistik($id)
    {
        $this->location_id = $id;
        $this->fligh_location = fligh_location::find($id);
        session(['location_id' => $id]);
        
        if (request()->routeIs('filament.admin.resources.fligh-locations.view')) {
            return; 
        }return redirect()->route('filament.admin.resources.fligh-locations.view', [
            'tenant' => Auth()->user()->teams()->first()->id,
            'record' => $id
        ]);
    }
}