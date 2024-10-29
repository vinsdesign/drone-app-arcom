<?php

namespace App\Filament\Pages;

use App\Filament\Resources\TeamsListResource;
use App\Models\team;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables;
use Filament\Resources\Pages\Page;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\IconColumn;

class TeamsList extends Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;
    protected static ?string $navigationIcon = 'heroicon-c-list-bullet';
    protected static string $resource = TeamsListResource::class;
    protected static string $view = 'filament.pages.teams-list';

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
            ->label('PT Name')
            ->searchable(),
            TextColumn::make('owner')
            ->label('Owner')
            ->searchable(),
            TextColumn::make('customers_count')
            ->label('Total Customers')
            ->counts('customers'),
            TextColumn::make('flighs_count')
            ->label('Total Flight')
            ->counts('flighs'),
            TextColumn::make('total_flight_duration')
            ->label('Total Flying Time')
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
                ->label('Show Details')
                ->modalHeading('Details Data')
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
