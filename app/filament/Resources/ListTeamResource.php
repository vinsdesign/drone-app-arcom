<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ListTeamResource\Pages;
use App\Filament\Resources\ListTeamResource\RelationManagers;
use App\Models\Team;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\Layout\View;
use Filament\Infolists\Components\View as InfoListView;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Helpers\TranslationHelper;

class ListTeamResource extends Resource
{
    protected static ?string $model = Team::class;
    protected static ?string $modelLabel = 'Team Lists';
    protected static ?string $navigationLabel = 'Team List';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public function getTableQuery()
    {
        return team::withCount('customers', 'flighs', 'drones', 'battreis', 'equidments', 'fligh_location');
    }
    public static function getNavigationItems(): array
    {
        $user = auth()->user();
        if ($user && !$user->hasRole(['super_admin'])) {
            return [];
        }
        return parent::getNavigationItems();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('avatar_url')
                    ->getStateUsing(fn($record) => $record->avatar_url ? asset('storage/' . $record->avatar_url) : 'asset/favicon.png')
                    ->circular()
                    ->size(100)
                    ->alignment('center'),
                TextColumn::make('name')
                    ->weight(FontWeight::Bold)
                    ->searchable()
                    ->sortable()
                    ->alignment('center'),
                Stack::make([
                    Panel::make([
                        View::make('component.table.team-list-table'),
                        View::make('component.chartjs.team-statistik'),
                    ]),
                    
                ])->collapsible(),
            ])->contentGrid([
                'md' => 1,
                'xl' => 2,
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            Section::make(TranslationHelper::translateIfNeeded('Team Overview'))
            ->description(fn($record) => 'Team Name: ' . $record->name)
                ->schema([
                    TextEntry::make('owner')->label(TranslationHelper::translateIfNeeded('Owner')),
                    TextEntry::make('customer_count')->label(TranslationHelper::translateIfNeeded('Total Customers'))
                    ->getStateUsing(fn($record) => $record->customers->count()),
                    TextEntry::make('flight_count')->label(TranslationHelper::translateIfNeeded('Total Flight'))
                    ->getStateUsing(fn($record) => $record->flighs->count()),
                    TextEntry::make('total_flight_duration')->label(TranslationHelper::translateIfNeeded('Total Flying Time'))
                        ->getStateUsing(fn($record) => $record->total_flight_duration),
                    TextEntry::make('drone_count')->label(TranslationHelper::translateIfNeeded('Total Drones'))
                    ->getStateUsing(fn($record) => $record->drones->count()),
                    TextEntry::make('battrei_count')->label(TranslationHelper::translateIfNeeded('Total Batteries'))
                    ->getStateUsing(fn($record) => $record->battreis->count()),
                    TextEntry::make('equipment_count')->label(TranslationHelper::translateIfNeeded('Total Equipment'))
                    ->getStateUsing(fn($record) => $record->equidments->count()),
                    TextEntry::make('location_count')->label(TranslationHelper::translateIfNeeded('Total Flight Locations'))
                    ->getStateUsing(fn($record) => $record->fligh_location->count()),
                    
                ])->columns(2),

                 Section::make('Team Statistics')
                    ->schema([
                        InfoListView::make('component.chartjs.team-statistik'),
                        // InfoListView::make('component.table.team-list-table')
                    ])
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
            'index' => Pages\ListListTeams::route('/'),
           'view' => Pages\ViewListTeams::route('/{record}'),
        ];
    }
    public static function isScopedToTenant(): bool
    {
        return false;
    }
}
