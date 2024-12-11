<?php
namespace App\Livewire;

use App\Models\fligh_location;
use App\Models\Projects;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Dotswan\MapPicker\Fields\Map;
use App\Helpers\TranslationHelper;

class MapLocationForm extends Component implements HasForms{
    use InteractsWithForms;
    public $latitude = -8.592113191530379;
    public $longitude = 115.27542114257814;
    public $latitudeVal;
    public $longitudeVal;
    
    public ?array $data = [];
    
    public function mount(): void
    {
        $this->form->fill();
    }

    
    public function form(Form $form): Form
    {
        $currentTeamId = Auth()->user()->teams()->first()->id;
        return $form 
        ->schema([
            TextInput::make('name')
                ->label(TranslationHelper::translateIfNeeded('Name'))
                ->required(),
            Select::make('Project')
                ->label(TranslationHelper::translateIfNeeded('Project'))
                ->options(function (callable $get) use ($currentTeamId) {
                    return Projects::where('teams_id', $currentTeamId)
                    ->where('status_visible', '!=', 'archived')
                    ->pluck('case', 'id');
                })
                ->afterStateUpdated(function ($state, callable $set) {
                    if ($state) {
                        $project = Projects::find($state);
                        $set('customers_id', $project ? $project->customers_id : null);
                        $set('customers_name', $project && $project->customers ? $project->customers->name : null);
                    } else {
                        $set('customers_id', null);
                        $set('customers_name', null);
                    }
                })
                ->reactive()
                ->searchable(),
            Hidden::make('customers_id'),
            TextInput::make('customers_name')
                    ->label(TranslationHelper::translateIfNeeded('Customers Name'))    
                    ->disabled()
                    ->afterStateHydrated(function ($state, $component, $record) {
                        if ($record) {
                            $customerId = \DB::table('fligh_locations')
                                ->where('id', $record->id)
                                ->value('customers_id'); 

                            if ($customerId) {
                                $customerName = \DB::table('customers')
                                    ->where('id', $customerId)
                                    ->value('name'); 
                
                                $component->state($customerName);
                            }
                        }
                    })
                    ->default(function (){
                        $currentTeam = auth()->user()->teams()->first();
                        return $currentTeam ? $currentTeam->id_customers  : null;
                    })
                    ->columnSpanFull(),
            
            Group::make([
                Group::make([
                    TextInput::make('latitude')
                    ->label(TranslationHelper::translateIfNeeded('Latitude'))->numeric()
                    ->extraAttributes(['id' => 'latitude-input']),
                TextInput::make('longitude')
                    ->label(TranslationHelper::translateIfNeeded('Longitude'))->numeric()
                    ->extraAttributes(['id' => 'longitude-input']),
               TextInput::make('altitude')
                    ->label(TranslationHelper::translateIfNeeded('Altitude'))
                    ->numeric()
                ])->columnSpan(1),
                
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
                    ->extraStyles(['border-radius: 20px'])
                    ->liveLocation(true, true, 5000)
                    ->showMarker()
                    ->showFullscreenControl()
                    ->showZoomControl()
                    ->draggable()
                    ->rangeSelectField('altitude')->columnSpan(1),
                
            ])->columns(2),

            TextInput::make('address')
            ->label(TranslationHelper::translateIfNeeded('Address'))    
            ->columnSpanFull(),
            TextInput::make('city')
            ->label(TranslationHelper::translateIfNeeded('City')),
            TextInput::make('pos_code')
            ->label(TranslationHelper::translateIfNeeded('Postal Code'))
            ->numeric(),
            TextInput::make('country')
            ->label(TranslationHelper::translateIfNeeded('Country')),
            Textarea::make('description')
            ->label(TranslationHelper::translateIfNeeded('Descriptions'))
            ->columnSpanFull(),
            
        ])->statePath('data');
    }
    public function create()
    {
       
        $state = $this->form->getState();
        

        $id = Auth()->user()->teams()->first()->id;
        $name = $state['name'] ?? null;
        $address = $state['address'] ?? null;
        $city = $state['city'] ?? null;
        $country = $state['country'] ?? null;
        $description = $state['description'] ?? null;
        $pos_code = $state['pos_code'] ?? null;
        $latitude = $state['latitude'] ?? null;
        $longitude = $state['longitude'] ?? null;
        $altitude =$state['altitude'] ?? null;
        $customers = $state['customers_id'] ?? null;
        $projects = $state['projects_id'] ?? null;

        
        
        $sql = new fligh_location([
                'name' =>$name,
                'description'=>$description,
                'address' =>$address,
                'city' =>$city,
                'country' =>$country,
                'pos_code' =>$pos_code,
                'latitude' =>$latitude,
                'longitude' =>$longitude,
                'altitude' =>$altitude,
                'teams_id' => $id,
                'customers_id' => $customers,
                'projects_id' => $projects,
            ]);
        if($sql->save()){
            $sql->teams()->attach($id);
            session()->flash('notification','success');
            return redirect()->route('filament.admin.resources.flighs.create',['tenant' =>$id]);
        }else{
            session()->flash('notificationError','error');
        }
    

    }
    
    public function render(): View
    {
        return view('livewire.map-location-form');
    }

}