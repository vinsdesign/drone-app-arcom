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
use Stichoza\GoogleTranslate\GoogleTranslate;


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
                Select::make('countries_id')->label(GoogleTranslate::trans('Country', session('locale') ?? 'en'))
                        ->options(countrie::all()->pluck('name','id'))
                        ->reactive()
                        ->afterStateUpdated(fn(callable $set)=>$set('cities_id',null))
                        ->placeholder((new GoogleTranslate(session('locale') ?? 'en'))->translate('Select a Country'))
                        ->searchable(),
                Select::make('cities_id')->label(GoogleTranslate::trans('City', session('locale') ?? 'en'))
                ->options(function ($get) {
                        $countryId = $get('countries_id');
                        if ($countryId) {
                            return citie::where('country_id', $countryId)->pluck('name', 'id');
                        }
                        return citie::pluck('name', 'id');
                    })
                ->searchable()
                ->reactive()
                ->placeholder((new GoogleTranslate(session('locale') ?? 'en'))->translate('Select a City'))
                ->disabled(fn ($get) => !$get('countries_id')),
                //phone
                TextInput::make('phone')->label(GoogleTranslate::trans('Phone', session('locale') ?? 'en'))
                ->tel()
                ->rules([
                    'unique:users,phone,' . ($this->record ? $this->record : 'NULL'),
                    'nullable',
                ])
                ->numeric(),
                TextInput::make('sertif')->label(GoogleTranslate::trans('Sertification', session('locale') ?? 'en'))
                        ->maxLength(255),
                TextArea::make('address')->label(GoogleTranslate::trans('Address', session('locale') ?? 'en'))
                ->helperText((new GoogleTranslate(session('locale') ?? 'en'))->translate('Your Specific Address'))

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
