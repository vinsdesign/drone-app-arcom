<?php
namespace App\Filament\Resources;
use App\Filament\Pages\ContactPage;
use Filament\Resources\Resource;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Pages\CustomPage;
use App\Filament\Pages\Report;
use App\Helpers\TranslationHelper;

class ReportResource extends Resource

{
    // protected static ?string $navigationLabel = 'Report';
    // protected static ?string $navigationGroup = 'Report';
    // protected static ?string $modelLabel = 'Report';
    protected static ?string $navigationIcon = 'heroicon-s-document-text';
    protected static bool $isLazy = false;
    public static function getNavigationLabel(): string
    {
        return TranslationHelper::translateIfNeeded('Report');
    }
    public static function getModelLabel(): string
    {
        return TranslationHelper::translateIfNeeded('Report');
    }

    public static function getPages(): array
    {
        return [
            'index' => Report::route('/'),

        ];
    }

    // Menonaktifkan table actions dan columns
    public static function table(Table $table): Table
    {
        return $table->columns([])  // Tidak menampilkan kolom
                     ->actions([]); // Tidak menampilkan aksi
    }
}
