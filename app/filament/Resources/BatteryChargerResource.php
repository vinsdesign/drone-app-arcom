<?php

namespace App\Filament\Resources;

use App\Filament\Pages\Charger;
use App\Filament\Pages\ImporterPage;
use App\Filament\Resources\ImporterResource\Pages;
use App\Filament\Resources\ImporterResource\RelationManagers;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BatteryChargerResource extends Resource
{
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static bool $shouldRegisterNavigation = false;


    public static function getPages(): array
    {
        return [
            'index' => Charger::route('/'),

        ];
    }

    public static function table(Table $table): Table
    {
        return $table->columns([])  
                     ->actions([]); 
    }
}
