<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FlighLocationResource\Pages;
use App\Filament\Resources\FlighLocationResource\RelationManagers;
use App\Models\customer;
use App\Models\fligh_location;
use App\Models\Projects;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Dotswan\MapPicker\Fields\Map;
use Filament\Support\Colors\Color;
use Stichoza\GoogleTranslate\GoogleTranslate;

class FlighLocationResource extends Resource
{
    protected static ?string $model = fligh_location::class;
    
    protected static ?string $navigationIcon = 'heroicon-s-map-pin';
    protected static?string $navigationGroup = 'Inventory';
    // protected static ?string $navigationLabel = 'Location';
    // protected static ?string $modelLabel = 'Locations';
    public static ?string $tenantOwnershipRelationshipName = 'teams';
    protected static ?string $tenantRelationshipName = 'fligh_location';
    protected static bool $isLazy = false;

    public static function getNavigationBadge(): ?string{
        return static::getModel()::count();
    }

    public static function getNavigationLabel(): string
    {
        return GoogleTranslate::trans('Locations', session('locale') ?? 'en');
    }
    public static function getModelLabel(): string
    {
        return GoogleTranslate::trans('Locations', session('locale') ?? 'en');
    }
    // public static function getNavigationGroup(): string
    // {
    //     return GoogleTranslate::trans('Inventory', session('locale') ?? 'en');
    // }

    public static function form(Form $form): Form
    {
        $currentTeamId = auth()->user()->teams()->first()->id;
        return $form
            ->schema([
                //untuk tenancy
                Forms\Components\Hidden::make('teams_id')
                ->default(auth()->user()->teams()->first()->id ?? null),
                //end untuk tenancy
                Forms\Components\Wizard::make([
                    Forms\Components\Wizard\Step::make(GoogleTranslate::trans('Location Overview', session('locale') ?? 'en'))
                    ->Schema([
                        Forms\Components\TextInput::make('name')->label(GoogleTranslate::trans('Name', session('locale') ?? 'en')),
                        Forms\Components\Select::make('projects_id')
                        ->label(GoogleTranslate::trans('Projects', session('locale') ?? 'en'))
                        // ->relationship('projects','case', function (Builder $query){
                        //     $currentTeamId = auth()->user()->teams()->first()->id;
                        //     $query->where('teams_id', $currentTeamId);
                        // }),  
                        ->options(function (callable $get) use ($currentTeamId) {
                            return projects::where('teams_id', $currentTeamId)->pluck('case', 'id');
                        })
                        ->label('Projects')
                        ->searchable(),  
                        Forms\Components\Select::make('customers_id')
                        // ->relationship('customers','name', function (Builder $query){
                        //     $currentTeamId = auth()->user()->teams()->first()->id;
                        //     $query->where('teams_id', $currentTeamId);
                        // })  
                        ->options(function (callable $get) use ($currentTeamId) {
                            return customer::where('teams_id', $currentTeamId)->pluck('name', 'id');
                        })  
                        ->label(GoogleTranslate::trans('Customers', session('locale') ?? 'en'))
                        ->searchable()
                        ->columnSpanFull(),
                        Forms\Components\TextArea::make('description')
                        ->label(GoogleTranslate::trans('Description', session('locale') ?? 'en'))
                        ->columnSpanFull(),
                    ])->columns(2),
                    Forms\Components\Wizard\Step::make(GoogleTranslate::trans('Location Address', session('locale') ?? 'en'))
                    ->Schema([
                        Forms\Components\TextInput::make('address')
                        ->label(GoogleTranslate::trans('Address', session('locale') ?? 'en'))    
                        ->columnSpanFull(),
                        Forms\Components\TextInput::make('city')
                        ->label(GoogleTranslate::trans('City', session('locale') ?? 'en')),
                        Forms\Components\TextInput::make('pos_code')
                        ->label(GoogleTranslate::trans('Postal Code', session('locale') ?? 'en'))
                        ->numeric(),
                        Forms\Components\TextInput::make('state')
                        ->label(GoogleTranslate::trans('State', session('locale') ?? 'en')),
                        Forms\Components\TextInput::make('country')
                        ->label(GoogleTranslate::trans('Country', session('locale') ?? 'en')),
                        Forms\Components\TextInput::make('latitude')
                        ->label(GoogleTranslate::trans('Latitude', session('locale') ?? 'en'))->numeric(),
                        Forms\Components\TextInput::make('longitude')
                        ->label(GoogleTranslate::trans('Longitude', session('locale') ?? 'en'))->numeric(),
                        Forms\Components\TextInput::make('altitude')
                        ->label(GoogleTranslate::trans('Altitude', session('locale') ?? 'en'))
                        ->numeric()
                        ->columnSpan('1'),
                        // Map::make('location')
                        // ->liveLocation(true, true, 5000)
                        // ->showMarker()
                        // ->markerColor("#22c55eff")
                        // ->showFullscreenControl()
                        // ->showZoomControl()
                        // ->draggable()
                        // ->tilesUrl("https://tile.openstreetmap.de/{z}/{x}/{y}.png")
                        // ->zoom(15)
                        // ->detectRetina()
                        // ->showMyLocationButton()
                        // ->geoMan(true)
                        // ->geoManEditable(true)
                        // ->geoManPosition('topleft')
                        // ->drawCircleMarker()
                        // ->rotateMode()
                        // ->drawMarker()
                        // ->drawPolygon()
                        // ->drawPolyline()
                        // ->drawCircle()
                        // ->dragMode()
                        // ->cutPolygon()
                        // ->editPolygon()
                        // ->deleteLayer()
                        // ->setColor('#3388ff')
                        // ->setFilledColor('#cad9ec')
                        
                    ])->columns(2)
                ])->columnSpanFull()

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label(GoogleTranslate::trans('Name', session('locale') ?? 'en'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('projects.case')->label(GoogleTranslate::trans('Project Case', session('locale') ?? 'en'))
                ->url(fn($record)  =>  $record->project_id ? route('filament.admin.resources.projects.index', [
                    'tenant' => Auth()->user()->teams()->first()->id,
                    'record' => $record->project_id,
                ]) : null)->color(Color::Blue)
                    ->searchable(),
                Tables\Columns\TextColumn::make('customers.name')->label(GoogleTranslate::trans('Customers Name', session('locale') ?? 'en'))
                ->url(fn($record) => $record->customer_id ? route('filament.admin.resources.customers.index', [
                    'tenant' => Auth()->user()->teams()->first()->id,
                    'record' => $record->customer_id,
                ]) : null)->color(Color::Blue)
                    ->searchable(),
                Tables\Columns\TextColumn::make('address')->label(GoogleTranslate::trans('Address', session('locale') ?? 'en'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('city')->label(GoogleTranslate::trans('City', session('locale') ?? 'en'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('pos_code')->label(GoogleTranslate::trans('Postal Code', session('locale') ?? 'en'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('state')->label(GoogleTranslate::trans('State', session('locale') ?? 'en'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('country')->label(GoogleTranslate::trans('Country', session('locale') ?? 'en'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('latitude')->label(GoogleTranslate::trans('Latitude', session('locale') ?? 'en'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('longitude')->label(GoogleTranslate::trans('Longitude', session('locale') ?? 'en'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('altitude')->label(GoogleTranslate::trans('Altitude', session('locale') ?? 'en'))
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status_visible')
                ->label('')
                ->options([
                    'current' => 'Current',
                    'archived' => 'Archived',
                ])
                ->default('current'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('Archive')->label('Archive')
                    ->hidden(fn ($record) => $record->status_visible == 'archived')
                            ->action(function ($record) {
                             $record->update(['status_visible' => 'archived']);
                             Notification::make()
                             ->title('Status Updated')
                             ->body("Status successfully changed.")
                             ->success()
                             ->send();
                        })->icon('heroicon-s-archive-box-arrow-down'),
                Tables\Actions\Action::make('Un-Archive')->label(' Un-Archive')
                    ->hidden(fn ($record) => $record->status_visible == 'current')
                            ->action(function ($record) {
                             $record->update(['status_visible' => 'current']);
                             Notification::make()
                             ->title('Status Updated')
                             ->body("Status successfully changed.")
                             ->success()
                             ->send();
                        })->icon('heroicon-s-archive-box'),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFlighLocations::route('/'),
            'create' => Pages\CreateFlighLocation::route('/create'),
            'edit' => Pages\EditFlighLocation::route('/{record}/edit'),
        ];
    }
}
