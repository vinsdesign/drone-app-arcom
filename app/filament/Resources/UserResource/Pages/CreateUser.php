<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
        //batasi berapa kali user bisa create data berdasarkan user/team bisa dikombinasikan dengan billling
    public static function boot()
    {
        $teams = auth()->user()->teams()->first()->id;
        $personnelCount = User::whereHas('teams',function ($query){
            $query->where('team_id', Auth()->user()->teams()->first()->id);
        })->count();
        $userSubscriotion =   Auth()->user()->planSubscriptions()->first()->name;
        if ($userSubscriotion === 'main') {
            if ($personnelCount >= 10) {
                Notification::make()
                    ->title('The Personnel creation limit has been reached')
                    ->danger()
                    ->body('You\'ve reached the maximum limit of 10 Personnel. Upgrade to another plan.')
                    ->send();
                
                return redirect()->route('filament.admin.resources.users.index',['tenant' => $teams]);
            }

        }elseif($userSubscriotion === 'paket 2')
        if ($personnelCount >= 50) {
            Notification::make()
                ->title('The Personnel creation limit has been reached')
                ->danger()
                ->body('You\'ve reached the maximum limit of 50 Personnel. Upgrade to another plan.')
                ->send();
            
            return redirect()->route('filament.admin.resources.users.index',['tenant' => $teams]);
        }
        
    }
    //end limitles
}
