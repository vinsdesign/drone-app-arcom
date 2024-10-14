<?php

namespace App\Livewire;

use Filament\Notifications\Notification;
use Jeffgreco13\FilamentBreezy\Livewire\MyProfileComponent;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Facades\Filament;
use App\Models\User;

class MyCustomComponent extends MyProfileComponent
{
    protected string $view = "vendor.filament-breezy.livewire.more-info";
    public array $only = ['phone', 'country', 'lenguage', 'sertif', 'timezone'];
    public array $data;
    public $user;

    public function mount()
    {
        $this->user = Filament::getCurrentPanel()->auth()->user();
        
        // Mengisi form dengan data user berdasarkan $only
        $this->form->fill($this->user->only($this->only));
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('phone')
                        ->tel()
                        ->unique(User::class, 'phone') // Validasi unique
                        ->rules(['unique:users,phone'])
                        ->numeric(),
                TextInput::make('country')
                        ->maxLength(255),
                TextInput::make('lenguage')
                        ->maxLength(255),
                TextInput::make('sertif')
                        ->maxLength(255),
                TextInput::make('timezone')
                        ->maxLength(255),
            ])
            ->statePath('data');
    }

    public function submit(): void
    {
        $data = collect($this->form->getState())->only($this->only)->all();
        $this->user->update($data);
        Notification::make()
            ->success()
            ->title(__('Updated successfully'))
            ->send();
    }
}
