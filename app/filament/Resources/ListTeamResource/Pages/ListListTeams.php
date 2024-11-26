<?php

namespace App\Filament\Resources\ListTeamResource\Pages;

use App\Filament\Resources\ListTeamResource;
use App\Livewire\HeaderWidget\HeaderTeam;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListListTeams extends ListRecords
{
    protected static string $resource = ListTeamResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
    protected function getHeaderWidgets(): array{
        return[
            HeaderTeam::class,
        ];
    }
}
