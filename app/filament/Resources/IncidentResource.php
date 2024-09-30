<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IncidentResource\Pages;
use App\Filament\Resources\IncidentResource\RelationManagers;
use App\Models\Incident;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class IncidentResource extends Resource
{
    protected static ?string $model = Incident::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Incident Overview')
                    ->description('')
                    ->schema([
                    Forms\Components\DatePicker::make('incident_date')
                    ->required(),
                    Forms\Components\TextInput::make('cause')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\Select::make('review')
                        ->required()
                        ->options([
                            false => 'Closed',
                            true => 'Under Review',
                        ]),
                    Forms\Components\BelongsToSelect::make('location_id')
                        ->relationship('fligh_location','name')
                        ->required()
                        ->searchable(),
                     Forms\Components\BelongsToSelect::make('drone_id')
                        ->relationship('drone','name')
                        ->required()
                        ->searchable(),
                    Forms\Components\BelongsToSelect::make('project_id')
                        ->relationship('project','name')
                        ->required()
                        ->searchable(),
                    Forms\Components\TextInput::make('personel_involved_id')
                        ->required()
                        ->numeric()->columnSpanFull(),
                    ])->columns(2),
                    //section 2
                Forms\Components\Section::make('Insiden Description')
                    ->description('')
                    ->schema([
                        Forms\Components\TextArea::make('aircraft_damage')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextArea::make('other_damage')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextArea::make('description')
                        ->required()
                        ->maxLength(255)->columnSpanFull(),
                    Forms\Components\TextInput::make('incuration_type')
                        ->required()
                        ->maxLength(255)->columnSpanFull(),
                    ])->columns(2),
                    //section 3
                Forms\Components\Section::make('Incident Rectification')
                ->description('')
                ->schema([
                    Forms\Components\TextInput::make('rectification_note')
                        ->required()
                        ->maxLength(255)->columnSpanFull(),
                    Forms\Components\DatePicker::make('rectification_date')
                        ->required(),
                    Forms\Components\TextInput::make('Technician')
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
                Tables\Columns\TextColumn::make('other_damage')
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->searchable(),
                Tables\Columns\TextColumn::make('incuration_type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('rectification_note')
                    ->searchable(),
                Tables\Columns\TextColumn::make('rectification_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('Technician')
                    ->searchable(),
                Tables\Columns\TextColumn::make('location_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('drone_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('project_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('personel_involved_id')
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
            'view' => Pages\ViewIncident::route('/{record}'),
            'edit' => Pages\EditIncident::route('/{record}/edit'),
        ];
    }
}
