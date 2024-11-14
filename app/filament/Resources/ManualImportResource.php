<?php
namespace App\Filament\Resources;

use App\Filament\Pages\ManualImport;
use Filament\Navigation\MenuItem;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use App\Helpers\TranslationHelper;

class ManualImportResource extends Resource

{
    protected static bool $shouldRegisterNavigation = false;
    // protected static ?string $navigationLabel = 'ManualImport';
    // protected static ?string $modelLabel = 'ManualImport';
    protected static ?string $navigationIcon = 'heroicon-o-cog';
    protected static bool $isLazy = false;

    public static function getNavigationLabel(): string
    {
        return TranslationHelper::translateIfNeeded('ManualImport');
    }
    public static function getModelLabel(): string
    {
        return TranslationHelper::translateIfNeeded('ManualImport');
    }

    public static function getPages(): array
    {
        return [
            'index' => ManualImport::route('/'),

        ];
    }

    public static function table(Table $table): Table
    {
        return $table->columns([])  
                     ->actions([]); 
    }
}
