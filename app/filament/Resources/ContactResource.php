<?php
namespace App\Filament\Resources;
use App\Filament\Pages\ContactPage;
use Filament\Resources\Resource;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Pages\CustomPage;
use App\Helpers\TranslationHelper;


class ContactResource extends Resource

{
    // protected static ?string $navigationLabel = 'Contact Us';
    // protected static ?string $navigationGroup = 'Contact';
    // protected static ?string $modelLabel = 'Contact';

    public static function getNavigationLabel(): string
    {
        return TranslationHelper::translateIfNeeded('Contact');
    }
    public static function getModelLabel(): string
    {
        return TranslationHelper::translateIfNeeded('Contact');
    }
    public static function getNavigationGroup(): string
    {
        return TranslationHelper::translateIfNeeded('Contact');
    }

    protected static ?string $navigationIcon = 'heroicon-m-chat-bubble-left-right';
    protected static bool $isLazy = false;
    public static function getPages(): array
    {
        return [
            'index' => ContactPage::route('/'),

        ];
    }

    // Menonaktifkan table actions dan columns
    public static function table(Table $table): Table
    {
        return $table->columns([])  // Tidak menampilkan kolom
                     ->actions([]); // Tidak menampilkan aksi
    }
}
