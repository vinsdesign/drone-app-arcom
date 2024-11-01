<?php

namespace App\Livewire;

use App\Models\citie;
use App\Models\countrie;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Notifications\Notification;
use Illuminate\Validation\Rule;
use Jeffgreco13\FilamentBreezy\Livewire\MyProfileComponent;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextArea;
use Filament\Forms\Form;
use Filament\Facades\Filament;
use App\Models\User;
use Storage;

class PersonalInfo extends MyProfileComponent
{
    protected string $view = "vendor.filament-breezy.livewire.personal-info";
    public array $only = ['avatar_url', 'name', 'email'];
    public array $data;
    public $user;
    public $record;
    public bool $hasAvatars = false;

    public function mount()
    {
        $this->hasAvatars = !empty($this->user->avatar_url);
        $this->user = Filament::getCurrentPanel()->auth()->user();
        $this->record =Auth()->user()->id;
        // Mengisi form dengan data user berdasarkan $only
        $this->form->fill($this->user->only($this->only));
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                FileUpload::make('avatar_url')->label('Avatar')
                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif'])
                ->helperText('Please choose image type jpg/jpeg/png')
                ->image() // This will render an image preview
                ->default(fn ($record) => $record->avatar_url ? Storage::url($record->avatar_url) : null),
                Grid::make(2)->schema([
                    TextInput::make('name')->label('Name'),
                    TextInput::make('email')->email()->label('Email')
                    ->rules(function ($get) {
                        return [
                            'email',
                            Rule::unique('teams', 'email')
                                ->ignore($get('id')),
                        ];
                    }),
                ]),
            ])->columns(2)
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
