<?php

namespace App\Filament\Resources\ProjectsResource\Pages;

use App\Filament\Resources\ProjectsResource;
use App\Models\Projects;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateProjects extends CreateRecord
{
    protected static string $resource = ProjectsResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl();
    }
    //batasi berapa kali user bisa create data berdasarkan user/team bisa dikombinasikan dengan billling
    public static function boot()
    {
        $teams = auth()->user()->teams()->first()->id;
        $projectCount = Projects::where('teams_id', $teams)->count();
        $userSubscriotion =   Auth()->user()->planSubscriptions()->first()->name;

        if ($userSubscriotion === 'main') {
            if ($projectCount >= 10) {
                Notification::make()
                    ->title('The project creation limit has been reached')
                    ->danger()
                    ->body('You\'ve reached the maximum limit of 10 projects. Upgrade to another plan.')
                    ->send();
                
                return redirect()->route('filament.admin.resources.projects.index',['tenant' => $teams]);
            }

        }elseif($userSubscriotion === 'paket 2')
        if ($projectCount >= 50) {
            Notification::make()
                ->title('The project creation limit has been reached')
                ->danger()
                ->body('You\'ve reached the maximum limit of 50 projects. Upgrade to another plan.')
                ->send();
            
            return redirect()->route('filament.admin.resources.projects.index',['tenant' => $teams]);
        }
        
    }
    //end limitles
}
