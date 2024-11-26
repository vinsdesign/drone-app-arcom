<?php

namespace App\Filament\Pages;

use App\Filament\Resources\TeamsListResource;
use App\Models\team;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables;
use Filament\Resources\Pages\Page;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use App\Helpers\TranslationHelper;

class TeamsList extends Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;
    protected static ?string $navigationIcon = 'heroicon-c-list-bullet';
    protected static string $resource = TeamsListResource::class;
    protected static string $view = 'filament.pages.teams-list';

    public function getHeading(): string
    {
        return TranslationHelper::translateIfNeeded('Teams List');
        // return GoogleTranslate::trans('Teams List', session('locale') ?? 'en');
    }
    public function getTitle(): string
    {
        return TranslationHelper::translateIfNeeded('Teams List');
        // return GoogleTranslate::trans('Teams List', session('locale') ?? 'en');
    }

    public function getTotalTeams(): int
    {
        return team::count();
    }

    public function getTableQuery()
    {
        return team::withCount('customers', 'flighs', 'drones', 'battreis', 'equidments', 'fligh_location');
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('name')
                ->label(TranslationHelper::translateIfNeeded('PT Name'))
                ->searchable(),
            TextColumn::make('owner')
                ->label(TranslationHelper::translateIfNeeded('Owner'))
                ->searchable(),
            TextColumn::make('customers_count')
                ->label(TranslationHelper::translateIfNeeded('Total Customers'))
                ->counts('customers'),
            TextColumn::make('flighs_count')
                ->label(TranslationHelper::translateIfNeeded('Total Flight'))
                ->counts('flighs'),
            TextColumn::make('total_flight_duration')
                ->label(TranslationHelper::translateIfNeeded('Total Flying Time'))
                ->getStateUsing(fn($record) => $record->total_flight_duration)
                ->sortable(),
            IconColumn::make('details')
                ->label(' ')
                ->icon('heroicon-o-eyes')
                ->action(fn($record) => $this->showDetailsModal($record))

            // Stack::make([
            //     Split::make([
            //         ImageColumn::make('avatar_url')
            //             ->circular(),
            //         TextColumn::make('name')
            //             ->weight(FontWeight::Bold)
            //             ->searchable()
            //             ->sortable(),
            //     ]),
            //     Panel::make([
            //         Stack::make([
            //             TextColumn::make('phone')
            //                 ->icon('heroicon-m-phone'),
            //             TextColumn::make('email')
            //                 ->icon('heroicon-m-envelope'),
            //         ]),
            //     ]),
            // ]),
        ];        
    }

    protected function getTableActions(): array
    {
        return [
            Action::make('showDetails')
                ->label(TranslationHelper::translateIfNeeded('Show Details'))
                ->modalHeading(TranslationHelper::translateIfNeeded('Details Data'))            
                ->modalSubheading(fn ($record) => "{$record->name}")
                ->modalContent(function ($record) {
                    return view('filament.modals.team-details', [
                        'owner' => $record->owner,
                        'customers_count' => $record->customers_count,
                        'flighs_count' => $record->flighs_count,
                        'total_flight_duration' => $record->total_flight_duration,
                        'drones_count' => $record->drones_count,
                        'battreis_count' => $record->battreis_count,
                        'equidments_count' => $record->equidments_count,
                        'fligh_location_count' => $record->fligh_location_count,
                    ]);
                })
                ->icon('heroicon-o-eye')
                ->modalSubmitAction(false) 
                ->modalCancelAction(false) 
        ];
    }
}
