<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FlighResource\Pages;
use App\Filament\Resources\FlighResource\RelationManagers;
use App\Models\Fligh;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FlighResource extends Resource
{
    protected static ?string $model = Fligh::class;



    public static ?string $tenantOwnershipRelationshipName = 'teams';

    protected static ?string $navigationLabel = 'Flights' ;

    Protected static ?string $modelLabel = 'Flights';

    protected static ?string $navigationIcon = 'heroicon-s-clipboard-document-list';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Flight Detail')
                    ->description('')
                    ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\DatePicker::make('date_flight')
                    ->required(),
                Forms\Components\TextInput::make('duration_hour')->label('Duration Hour')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('duration_minute')->label('Duration Minute')
                    ->required()
                    ->numeric(),
                Forms\Components\Select::make('type')->label('Flight Type')
                    ->options([
                        'commercial-agriculture' => 'Commercial-Agriculture',
                        'commercial-inspection' => 'Commercial-Inspection',
                        'commercial-mapping/survey' => 'Commercial-Mapping/Survey',
                        'commercial-other' => 'Commercial-Other',
                        'commercial-photo/video' => 'Commercial-Photo/Video',
                        'emergency' => 'Emergency',
                        'hobby-entertainment' => 'Hobby-Entertainment',
                        'maintenance' => 'Maintenance',
                        'mapping_hr' => 'Mapping HR',
                        'mapping_uhr' => 'Mapping UHR',
                        'photogrammetry' => 'Photogrammetry',
                        'science' => 'Science',
                        'search_rescue' => 'Seach and Rescue',
                        'simulator' => 'Simulator',
                        'situational_awareness' => 'Situational Awareness',
                        'spreading' => 'Spreading',
                        'surveillance/patrol' => 'Surveillance or Patrol',
                        'survey' => 'Survey',
                        'test_flight' => 'Test Flight',
                        'training_flight' => 'Training Flight',
                    ])
                    ->required(),
                Forms\Components\Select::make('ops')->label('Ops')
                    ->options([
                        'vlos(manual)' => 'VLOS(Manual)',
                        'vlos_autonomous' => 'VLOS Autonomous',
                        'automatic' => 'Automatic',
                        'vlos_lts' => 'VLOS LTS',
                        'evlos' => 'EVLOS',
                        'bvlos/blos' => 'BVLOS/BLOS',
                        'tethered' => 'Tethered',
                        'fvp' => 'FVP',
                        'over_people' => 'Over People',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('landings')
                    ->required()
                    ->numeric(),
                Forms\Components\Select::make('customers_id')
                    ->relationship('customers', 'name')
                    ->required(),
                //Forms\Components\Select::make('location_id')
                    //->relationship('fligh_locations', 'name'),
                    //->required(),
                Forms\Components\Select::make('projects_id')
                    ->relationship('projects', 'case')
                    ->required(),
                ])->columns(3),
                Forms\Components\Section::make('Personnel')
                    ->description('')
                    ->schema([
                Forms\Components\Select::make('users_id')->label('Pilot')
                    ->relationship('users', 'name', function (Builder $query) {
                        $query->where('role', 'pilot');
                    })    
                    ->required(),
                Forms\Components\TextInput::make('instructor')->label('Instructor')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('vo')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('po')
                    ->required()
                    ->maxLength(255),
                ])->columns(2),
                Forms\Components\Section::make('Drone & Equipments')
                    ->description('')
                    ->schema([
                Forms\Components\Select::make('drones_id')
                    ->relationship('drones', 'name')    
                    ->required(),
                Forms\Components\Select::make('battreis_id')->label('Battery')
                    ->relationship('battreis', 'name')    
                    ->required(),
                Forms\Components\Select::make('equidments_id')->label('Equipment')
                    ->relationship('equidments', 'name')   
                    ->required(),
                Forms\Components\TextInput::make('pre_volt')->label('Pre Voltage')
                    ->numeric()    
                    ->required(),
                Forms\Components\TextInput::make('fuel_used')
                    ->numeric()    
                    ->required(),
                    ])->columns(2),
                //Forms\Components\TextInput::make('wheater_id')
                    //->required()
                    //->numeric(),
                Forms\Components\Hidden::make('teams_id')
                    ->default(auth()->user()->teams()->first()->id ?? null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('date_flight')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('duration_hour'),
                Tables\Columns\TextColumn::make('duration_minute'),
                Tables\Columns\TextColumn::make('customers.name')
                    ->numeric()
                    ->sortable(),
                //Tables\Columns\TextColumn::make('location_id')
                    //->numeric()
                    //->sortable(),
                Tables\Columns\TextColumn::make('projects.case')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('users.name')
                    ->label('Pilot')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
        
        ->schema([
            Section::make('Flight Detail')
                ->schema([
                TextEntry::make('name')->label('Name'),
                TextEntry::make('date_flight')->label('Date Flight'),
                TextEntry::make('duration_hour')->label('Duration Hour'),
                TextEntry::make('duration_minute')->label('Duration Minute'),
                TextEntry::make('type')->label('Type'),
                TextEntry::make('ops')->label('Ops'),
                TextEntry::make('landings')->label('Landings'),
                TextEntry::make('customers.name')->label('Customer'),
                TextEntry::make('projects.case')->label('Project'),
                ])->columns(5),
            Section::make('Personnel')
                ->schema([
                TextEntry::make('users.name')->label('Pilot'),
                TextEntry::make('instructor')->label('Instructor'),
                TextEntry::make('vo')->label('VO'),
                TextEntry::make('po')->label('PO'),
                ])->columns(4),
            Section::make('Drone & Equipments')
                ->schema([
                TextEntry::make('drones.name')->label('Drone'),
                TextEntry::make('battreis.name')->label('Battery'),
                TextEntry::make('equidments.name')->label('Equipment'),
                TextEntry::make('pre_volt')->label('Pre-Voltage'),
                TextEntry::make('fuel_used')->label('Fuel Used'),
                ])->columns(5)
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
            'index' => Pages\ListFlighs::route('/'),
            'create' => Pages\CreateFligh::route('/create'),
            'edit' => Pages\EditFligh::route('/{record}/edit'),
        ];
    }
}
