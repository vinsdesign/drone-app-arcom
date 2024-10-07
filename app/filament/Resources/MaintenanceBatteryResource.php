<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MaintenanceBatteryResource\Pages;
use App\Filament\Resources\MaintenanceBatteryResource\RelationManagers;
use App\Models\maintence_eq;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MaintenanceBatteryResource extends Resource
{
    protected static ?string $model = maintence_eq::class;

    protected static ?string $navigationLabel = 'Maintenance Equipment';
    protected static ?string $tenantRelationshipName = 'maintence_eqs';


    protected static ?string $navigationIcon = 'heroicon-o-cog';
    public static ?string $tenantOwnershipRelationshipName = 'teams';

    

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Maintenance Battery Overview')
                    ->schema([
                        Forms\Components\Hidden::make('teams_id')
                        ->default(auth()->user()->teams()->first()->id ?? null),
                        Forms\Components\TextInput::make('name')
                            ->label('Maintenance Description')
                            ->maxLength(255),
                        Forms\Components\Select::make('equidment_id')
                            ->relationship('equidment','name')
                            ->columnSpan(1),
                        Forms\Components\DatePicker::make('date')
                            ->label('Maintenance Date')   
                            ->columnSpan(1),
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'asset'=> 'Schedule',
                                'in_progress'=> 'In Progress',
                                'completed'=> 'Completed',
                            ]),
                        Forms\Components\TextInput::make('cost')
                            ->label('Expense Cost'),
                        Forms\Components\TextInput::make('currency')
                            ->label('Currency'),
                        Forms\Components\TextArea::make('notes')
                            ->label('Notes')
                            ->columnSpanFull(),
                ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                    
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
            'index' => Pages\ListMaintenanceBatteries::route('/'),
            'create' => Pages\CreateMaintenanceBattery::route('/create'),
            'edit' => Pages\EditMaintenanceBattery::route('/{record}/edit'),
        ];
    }
}
