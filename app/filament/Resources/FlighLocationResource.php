<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FlighLocationResource\Pages;
use App\Filament\Resources\FlighLocationResource\RelationManagers;
use App\Models\fligh_location;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Dotswan\MapPicker\Fields\Map;

class FlighLocationResource extends Resource
{
    protected static ?string $model = fligh_location::class;

    protected static ?string $navigationIcon = 'heroicon-s-map-pin';
    protected static?string $navigationGroup = 'Inventory';
    protected static ?string $navigationLabel = 'Location';
    protected static ?string $modelLabel = 'Locations';
    public static ?string $tenantOwnershipRelationshipName = 'teams';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //untuk tenancy
                Forms\Components\Hidden::make('teams_id')
                ->default(auth()->user()->teams()->first()->id ?? null),
                //end untuk tenancy
                Forms\Components\Wizard::make([
                    Forms\Components\Wizard\Step::make('Location Overview')
                    ->Schema([
                        Forms\Components\TextInput::make('name'),
                        Forms\Components\Select::make('projects_id')
                        ->relationship('projects','case', function (Builder $query){
                            $currentTeamId = auth()->user()->teams()->first()->id;
                            $query->where('teams_id', $currentTeamId);
                        }),    
                        Forms\Components\Select::make('customers_id')
                        ->relationship('customers','name', function (Builder $query){
                            $currentTeamId = auth()->user()->teams()->first()->id;
                            $query->where('teams_id', $currentTeamId);
                        })    
                        ->columnSpanFull(),
                        Forms\Components\TextArea::make('description')->columnSpanFull(),
                    ])->columns(2),
                    Forms\Components\Wizard\Step::make('Location Address')
                    ->Schema([
                        Forms\Components\TextInput::make('address')->columnSpanFull(),
                        Forms\Components\TextInput::make('city'),
                        Forms\Components\TextInput::make('pos_code')->numeric(),
                        Forms\Components\TextInput::make('state'),
                        Forms\Components\TextInput::make('country'),
                        Forms\Components\TextInput::make('latitude')->numeric(),
                        Forms\Components\TextInput::make('longitude')->numeric(),
                        Forms\Components\TextInput::make('altitude')->numeric()->columnSpan('1'),
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
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('project_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('customer_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('address')
                    ->searchable(),
                Tables\Columns\TextColumn::make('city')
                    ->searchable(),
                Tables\Columns\TextColumn::make('pos_code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('state')
                    ->searchable(),
                Tables\Columns\TextColumn::make('country')
                    ->searchable(),
                Tables\Columns\TextColumn::make('latitude')
                    ->searchable(),
                Tables\Columns\TextColumn::make('longitude')
                    ->searchable(),
                Tables\Columns\TextColumn::make('altitude')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
