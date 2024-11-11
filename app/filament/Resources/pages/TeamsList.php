<?php

namespace App\Filament\Pages;

use App\Filament\Resources\TeamsListResource;
use App\Models\team;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables;
use Filament\Resources\Pages\Page;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Stichoza\GoogleTranslate\GoogleTranslate;

class TeamsList extends Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;
    protected static ?string $navigationIcon = 'heroicon-c-list-bullet';
    protected static string $resource = TeamsListResource::class;
    protected static string $view = 'filament.pages.teams-list';

    public function getHeading(): string
    {
        return GoogleTranslate::trans('Teams List', session('locale') ?? 'en');
    }
    public function getTitle(): string
    {
        return GoogleTranslate::trans('Teams List', session('locale') ?? 'en');
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
            ->label((new GoogleTranslate(session('locale') ?? 'en'))->translate('PT Name'))
            ->searchable(),
            TextColumn::make('owner')
            ->label((new GoogleTranslate(session('locale') ?? 'en'))->translate('Owner'))
            ->searchable(),
            TextColumn::make('customers_count')
            ->label((new GoogleTranslate(session('locale') ?? 'en'))->translate('Total Customers'))
            ->counts('customers'),
            TextColumn::make('flighs_count')
            ->label((new GoogleTranslate(session('locale') ?? 'en'))->translate('Total Flight'))
            ->counts('flighs'),
            TextColumn::make('total_flight_duration')
            ->label((new GoogleTranslate(session('locale') ?? 'en'))->translate('Total Flying Time'))
            ->getStateUsing(fn($record) => $record->total_flight_duration)
            ->sortable(),
            IconColumn::make('details')
                ->label(' ')
                ->icon('heroicon-o-eyes')
                ->action(fn($record) => $this->showDetailsModal($record))
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Action::make('showDetails')
                ->label((new GoogleTranslate(session('locale') ?? 'en'))->translate('Show Details'))
                ->modalHeading((new GoogleTranslate(session('locale') ?? 'en'))->translate('Details Data'))
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
