<?php
namespace App\Filament\Resources;
use App\Filament\Pages\ContactPage;
use Filament\Resources\Resource;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Pages\CustomPage;
use App\Filament\Pages\TeamsList;
use App\Helpers\TranslationHelper;

class TeamsListResource extends Resource

{
    // protected static ?string $navigationLabel = 'Teams List';
    // protected static ?string $navigationGroup = 'Teams List';
    // protected static ?string $modelLabel = 'Teams List';
    protected static ?string $navigationIcon = 'heroicon-c-list-bullet';
    protected static bool $isLazy = false;
    protected static bool $shouldRegisterNavigation = false;

    public static function getNavigationLabel(): string
    {
        return TranslationHelper::translateIfNeeded('Teams List');
    }
    public static function getModelLabel(): string
    {
        return TranslationHelper::translateIfNeeded('Teams List');
    }
    public static function getNavigationGroup(): string
    {
        return TranslationHelper::translateIfNeeded('Teams List');
    }

    public static function getPages(): array
    {
        return [
            'index' => TeamsList::route('/'),

        ];
    }

    public static function table(Table $table): Table
    {
        return $table->columns([])  
                     ->actions([]); 
    }
}
