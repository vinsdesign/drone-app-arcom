<?php
namespace App\Filament\Widgets;

use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\View;
use Filament\Infolists\Infolist;
use Filament\Widgets\Widget;
use App\Livewire\HeaderWidget\HeaderProject;

class TabColum extends Widget
{
    protected int|string|array $columnSpan = 'full';
    protected static string $view = 'filament.widgets.tab-widget'; // Pastikan path ini benar

    public function render(): \Illuminate\Contracts\View\View
    {
        return view(static::$view, [
            'infolist' => $this->getInfolist()->render(),
        ]);
    }

    protected function getInfolist(): Infolist
    {
        return Infolist::make()->schema([
            Tabs::make('Tabs')
                ->tabs([
                    Tabs\Tab::make('Summary')
                        ->schema([
                            // View::make(''),
                            
                        ]),
                    Tabs\Tab::make('Flight')
                        ->schema([
                            // view::make()
                        ]),
                        Tabs\Tab::make('maintenance')
                        ->schema([
                            // view::make()
                        ]),
                        Tabs\Tab::make('Inventory')
                        ->schema([
                            // view::make()
                        ]),
                        Tabs\Tab::make('Document')
                        ->schema([
                            // view::make()
                        ]),
                        Tabs\Tab::make('Incident')
                        ->schema([
                            // view::make()
                        ]),
                ])
                ->activeTab(1),
        ]);
    }
}
