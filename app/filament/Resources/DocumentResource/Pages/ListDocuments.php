<?php

namespace App\Filament\Resources\DocumentResource\Pages;

use App\Filament\Resources\DocumentResource;
use App\Livewire\HeaderWidget\HeaderDocument;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListDocuments extends ListRecords
{
    protected static string $resource = DocumentResource::class;
    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
    protected function getHeaderWidgets(): array{
        return [
            HeaderDocument::class,
        ];
    }
    // public function getTabs(): array
    // {
    //     return [
    //         'All' => Tab::make()->modifyQueryUsing(function(Builder $query){
    //             $query->where('status_visible',null)
    //             ->orderBy('created_at','desc');
    //         }),
    //         'Archived' => Tab::make()->modifyQueryUsing(function(Builder $query){
    //             $query->where('status_visible','archived')
    //             ->orderBy('created_at','desc');
    //         }),

    //     ];
    // }
}
