<?php
namespace App\Filament\Resources;
use App\Filament\Pages\ContactPage;
use Filament\Resources\Resource;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Pages\CustomPage;


class ContactResource extends Resource

{
    protected static ?string $navigationLabel = 'Contact Us';
    protected static ?string $navigationGroup = 'Contact';
    protected static ?string $modelLabel = 'Contact';
    protected static ?string $navigationIcon = 'heroicon-m-chat-bubble-left-right';
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
