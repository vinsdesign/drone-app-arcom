<?php
namespace App\Filament\Resources\FlighResource\Pages;

use App\Filament\Resources\FlighResource;
use Filament\Actions;
use Filament\Forms\Components\Builder;
use Filament\Resources\Pages\ViewRecord;

class ViewFligh extends ViewRecord
{
    protected static string $resource = FlighResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\EditAction::make(),
        ];
    }
}
