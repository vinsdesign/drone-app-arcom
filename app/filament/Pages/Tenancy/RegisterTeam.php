<?php
namespace App\Filament\Pages\Tenancy;
 
use App\Models\Team;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Tenancy\RegisterTenant;
 
class RegisterTeam extends RegisterTenant
{
    public static function getLabel(): string
    {
        return 'Register team';
    }
 
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name'),
                // ...
            ]);
    }

    public function registerTeam(array $data)
    {
       
        $team = $this->handleRegistration($data);

        return $this->success($team);
    }
 
    protected function handleRegistration(array $data): Team
    {
        $team = Team::create($data);
 
        $team->users()->attach(auth()->user());
    
        return $team;
    }

    // untuk hendel registration team
    public static function boot(){
        $user = auth()->user()->teams()->first()->id ?? 0;
        $teams = auth()->user()->teams()->first()->id ?? 0; 
        if(!$user == 0){
            return redirect()->route('filament.admin.pages.dashboard',['tenant' => $teams]);
        }
    }
}