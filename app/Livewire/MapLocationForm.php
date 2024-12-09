<?php
namespace App\Livewire;

use Filament\Forms\Form;
use Livewire\Component;
use Filament\Facades\Filament;
use Dotswan\MapPicker\Fields\Map;

class MapLocationForm extends Component{

    public $latitude = -8.592113191530379;
    public $longitude = 115.27542114257814;

    protected static string $view = 'livewire.map-location-form';

    
    public function form(Form $form): Form
    {
        return $form 
        ->schema([
            Map::make('locationMaps')
                ->label('Maps')
                ->columnSpanFull()
                ->defaultLocation($this->latitude, $this->longitude)
                ->afterStateHydrated(function ($state, $record, callable $set) {
                    if ($record) {
                        $set('locationMaps', [
                            'lat' => $record->latitude,
                            'lng' => $record->longitude,
                        ]);
                    }
                })
                ->afterStateUpdated(function ($state, callable $set): void {
                    $set('latitude',  $state['lat']);
                    $set('longitude', $state['lng']);
                })
                ->extraStyles(['min-height: 50vh', 'border-radius: 20px'])
                ->liveLocation(true, true, 5000)
                ->showMarker()
                ->showFullscreenControl()
                ->showZoomControl()
                ->draggable()
                ->rangeSelectField('altitude'),
        ]);
    }
    // public function render()
    // {
    //     return view('livewire.map-location-form',[
    //         'form' => $this->form,
    //     ]);
    // }

}