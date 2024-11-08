<?php
namespace App\Filament\Resources;

use App\Filament\Pages\Settings as PagesSettings;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Stichoza\GoogleTranslate\GoogleTranslate;

class SettingsResource extends Resource

{
    // protected static ?string $navigationLabel = 'Settings';
    // protected static ?string $navigationGroup = 'Settings';
    // protected static ?string $modelLabel = 'Settings';
    protected static ?string $navigationIcon = 'heroicon-o-cog';
    protected static bool $isLazy = false;

    public static function getNavigationLabel(): string
    {
        return GoogleTranslate::trans('Settings', session('locale') ?? 'en');
    }
    public static function getModelLabel(): string
    {
        return GoogleTranslate::trans('Settings', session('locale') ?? 'en');
    }
    public static function getNavigationGroup(): string
    {
        return GoogleTranslate::trans('Settings', session('locale') ?? 'en');
    }
    public static function getPages(): array
    {
        return [
            'index' => PagesSettings::route('/'),

        ];
    }

    public static function table(Table $table): Table
    {
        return $table->columns([])  
                     ->actions([]); 
    }
}
