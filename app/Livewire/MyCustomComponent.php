<?php

namespace App\Livewire;

use App\Models\citie;
use App\Models\countrie;
use Filament\Notifications\Notification;
use Jeffgreco13\FilamentBreezy\Livewire\MyProfileComponent;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextArea;
use Filament\Forms\Form;
use Filament\Facades\Filament;
use App\Models\User;

class MyCustomComponent extends MyProfileComponent
{
    protected string $view = "vendor.filament-breezy.livewire.more-info";
    public array $only = ['phone', 'countries_id', 'cities_id', 'sertif', 'address'];
    public array $data;
    public $user;
    public $record;

    public function mount()
    {
        $this->user = Filament::getCurrentPanel()->auth()->user();
        $this->record =Auth()->user()->id;
        // Mengisi form dengan data user berdasarkan $only
        $this->form->fill($this->user->only($this->only));
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('countries_id')->label('Country')
                        ->options(countrie::all()->pluck('name','id'))
                        ->reactive()
                        ->afterStateUpdated(fn(callable $set)=>$set('cities_id',null))
                        ->placeholder('Select a Country')
                        ->searchable(),
                Select::make('cities_id')->label('City')
                ->options(function ($get) {
                        $countryId = $get('countries_id');
                        if ($countryId) {
                            return citie::where('country_id', $countryId)->pluck('name', 'id');
                        }
                        return citie::pluck('name', 'id');
                    })
                ->searchable()
                ->reactive()
                ->placeholder('Select a City')
                ->disabled(fn ($get) => !$get('countries_id')),
                //phone
                TextInput::make('phone')->label('Phone')
                ->tel()
                ->rules([
                    'unique:users,phone,' . ($this->record ? $this->record : 'NULL'),
                    'nullable',
                ])
                ->numeric(),
                TextInput::make('sertif')->label('Sertification')
                        ->maxLength(255),
                TextArea::make('address')->label('Address')
                ->helperText('Your Specific Address')

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
