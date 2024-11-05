<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IncidentResource\Pages;
use App\Filament\Resources\IncidentResource\RelationManagers;
use App\Models\drone;
use App\Models\fligh;
use App\Models\fligh_location;
use App\Models\Incident;
use App\Models\project;
use App\Models\User;
use Filament\Forms;
use Filament\Infolists\Components\Section;
use Filament\Forms\Form;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Support\Colors\Color;
use Filament\Tables\Actions\Action;

class IncidentResource extends Resource
{
    protected static ?string $model = Incident::class;
    protected static ?string $navigationLabel = 'Incident';

    protected static ?string $navigationIcon = 'heroicon-m-exclamation-triangle';
    public static ?string $tenantOwnershipRelationshipName = 'teams';
    public static ?int $navigationSort = 7;
    public static ?string $navigationGroup = 'flight';
    protected static bool $isLazy = false;


    public static function form(Form $form): Form
    {
        $currentTeamId = auth()->user()->teams()->first()->id;
        return $form
            ->schema([
                Forms\Components\Section::make('Incident Overview')
                    ->description('')
                    ->schema([
                    Forms\Components\Hidden::make('teams_id')
                        ->default(auth()->user()->teams()->first()->id ?? null),
                    Forms\Components\DatePicker::make('incident_date')
                    ->required(),
                    Forms\Components\TextInput::make('cause')
                        ->label('Incident Cause')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\Select::make('status')
                        ->required()
                        ->options([
                            false => 'Closed',
                            true => 'Under Review',
                        ]),
                    // Forms\Components\BelongsToSelect::make('location_id')
                    Forms\Components\Select::make('location_id')
                        // ->relationship('fligh_locations', 'name')
                        ->options(function (callable $get) use ($currentTeamId) {
                            return fligh_location::where('teams_id', $currentTeamId)->pluck('name', 'id');
                        })
                        ->searchable()
                        ->label('Flight Locations')
                        ->required(),
                        // ->searchable(),
                     Forms\Components\Select::make('drone_id')
                        // ->relationship('drone','name')
                        ->label('Drones')
                        ->options(function (callable $get) use ($currentTeamId) {
                            return drone::where('teams_id', $currentTeamId)->pluck('name', 'id');
                        })
                        ->searchable()
                        ->required(),
                    Forms\Components\Select::make('project_id')
                        // ->relationship('project','case')
                        ->label('Projects')
                        ->options(function (callable $get) use ($currentTeamId) {
                            return project::where('teams_id', $currentTeamId)->pluck('case', 'id');
                        })
                        ->searchable()
                        ->required(),
                    Forms\Components\Select::make('personel_involved_id')->label('Organization Personnel Involved ')
                        ->options(
                            function (Builder $query) use ($currentTeamId) {
                                return User::whereHas('teams', function (Builder $query) use ($currentTeamId) {
                                    $query->where('team_user.team_id', $currentTeamId); 
                            })->pluck('name','id');
                        }  
                        )->searchable()
                        ->columnSpanFull(),
                    ])->columns(2),
                    //section 2
                Forms\Components\Section::make('Insiden Description')
                    ->description('')
                    ->schema([
                        Forms\Components\TextArea::make('aircraft_damage')->label('Aircraft Damage')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextArea::make('other_damage')->label('Other Damage')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextArea::make('description')->label('Description')
                        ->required()
                        ->maxLength(255)->columnSpanFull(),
                    Forms\Components\TextInput::make('incuration_type')->label('Incursions (people, aircraft...)')
                        ->required()
                        ->maxLength(255)->columnSpanFull(),
                    ])->columns(2),
                    //section 3
                Forms\Components\Section::make('Incident Rectification')
                ->description('')
                ->schema([
                    Forms\Components\TextInput::make('rectification_note')->label('Rectification Notes')
                        ->required()
                        ->maxLength(255)->columnSpanFull(),
                    Forms\Components\DatePicker::make('rectification_date')->label('Rectification Date')
                        ->required(),
                    Forms\Components\TextInput::make('Technician')->label('Technician')
                        ->required()
                        ->maxLength(255),
                ])->columns(2),

            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('incident_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cause')
                    ->searchable(),

                Tables\Columns\TextColumn::make('aircraft_damage')
                    ->searchable(),

                // Tables\Columns\TextColumn::make('other_damage')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('description')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('incuration_type')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('rectification_note')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('rectification_date')
                //     ->date()
                //     ->sortable(),
                Tables\Columns\TextColumn::make('Technician')
                    ->searchable(),

                // Tables\Columns\TextColumn::make('location_id')
                //     ->numeric()
                //     ->sortable(),

                Tables\Columns\TextColumn::make('drone.name')
                    ->numeric()
                    ->url(fn($record) => $record->drone_id?route('filament.admin.resources.drones.view', [
                        'tenant' => Auth()->user()->teams()->first()->id,
                        'record' => $record->drone_id,
                    ]):null)->color(Color::Blue)
                    ->sortable(),
                Tables\Columns\TextColumn::make('project.case')
                    ->numeric()
                    ->url(fn($record) => $record->project_id?route('filament.admin.resources.projects.index', [
                        'tenant' => Auth()->user()->teams()->first()->id,
                        'record' => $record->project_id,
                    ]):null)->color(Color::Blue)
                    ->sortable(),
                // Tables\Columns\TextColumn::make('personel_involved_id')
                //     ->numeric()
                //     ->sortable(),
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
                // Action::make('viewFlight')
                // ->label('View Flight')
                // ->url(function ($record) {
                //     $flight = fligh::where('projects_id', $record->project_id)
                //         ->where('location_id', $record->location_id)
                //         ->where('drones_id', $record->drone_id)
                //         ->orderBy('start_date_flight', 'desc')
                //         ->first();

                //     if (!$flight) {
                //             $flight = fligh::where('drones_id', $record->drone_id)
                //                 ->orderBy('start_date_flight', 'desc')
                //                 ->first();
                //         }

                //     return $flight
                //         ? route('filament.admin.resources.flighs.view', [
                //             'tenant' => auth()->user()->teams()->first()->id,
                //             'record' => $flight->id,
                //         ])
                //         : null; 
                // })
                // ->button()
                // ->requiresConfirmation(),
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
            Section::make('Incident Overview')
                ->schema([
                    TextEntry::make('incident_date'),
                    TextEntry::make('cause'),
                    TextEntry::make('status'),
                    TextEntry::make('location_id'),
                    TextEntry::make('drone.name')
                        ->url(fn($record) => $record->drone_id?route('filament.admin.resources.drones.view', [
                            'tenant' => Auth()->user()->teams()->first()->id,
                            'record' => $record->drone_id,
                        ]):null)->color(Color::Blue),
                    TextEntry::make('project.case')
                        ->url(fn($record) => $record->project_id?route('filament.admin.resources.projects.index', [
                            'tenant' => Auth()->user()->teams()->first()->id,
                            'record' => $record->project_id,
                        ]):null)->color(Color::Blue),
                    TextEntry::make('personel_involved_id'),
                ])->columns(4),
            Section::make('Insiden Description')
                ->schema([
                    TextEntry::make('aircraft_damage'),
                    TextEntry::make('other_damage'),
                    TextEntry::make('description'),
                    TextEntry::make('incuration_type'),
                ])->columns(4),
            Section::make('Incident Rectification')
                ->schema([
                    TextEntry::make('rectification_note'),
                    TextEntry::make('rectification_date'),
                    TextEntry::make('Technician'),
                ])->columns(3)
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
            'index' => Pages\ListIncidents::route('/'),
            'create' => Pages\CreateIncident::route('/create'),

            //'view' => Pages\ViewIncident::route('/{record}'),

            'edit' => Pages\EditIncident::route('/{record}/edit'),
        ];
    }
}
