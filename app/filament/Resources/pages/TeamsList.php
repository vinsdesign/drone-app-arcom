<?php

namespace App\Filament\Pages;

use App\Filament\Resources\TeamsListResource;
use App\Models\team;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables;
use Filament\Resources\Pages\Page;

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
        return team::withCount('customers', 'flighs');
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
            // TextColumn::make('address')
            // ->label('Address')
            // ->searchable()
        ];
    }
}
