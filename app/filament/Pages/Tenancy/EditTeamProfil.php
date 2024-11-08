<?php
namespace App\Filament\Pages\Tenancy;
 
use App\Models\citie;
use App\Models\countrie;
use App\Models\team;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextArea;
use Filament\Forms\Form;
use Filament\Pages\Tenancy\EditTenantProfile;
use Illuminate\Validation\Rule;
use Stichoza\GoogleTranslate\GoogleTranslate;
 
class EditTeamProfil extends EditTenantProfile
{
    public static function getLabel(): string
    {
        return 'Team profile';
    }
 
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Organization Information')
                ->description('')
                ->schema([
                    FileUpload::make('avatar_url')->label(GoogleTranslate::trans('Your Avatar', session('locale')))
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif'])
                    ->helperText((new GoogleTranslate(session('locale') ?? 'en'))->translate('Please choose image type jpg/jpeg/png')),
                    TextInput::make('name')->label(GoogleTranslate::trans('Organization Name', session('locale')))->columnSpan(2),
                    TextInput::make('email')->email()->label(GoogleTranslate::trans('Email Address', session('locale')))
                    ->rules(function ($get) {
                        return [
                            'email',
                            Rule::unique('teams', 'email')
                                ->ignore($get('id')),
                        ];
                    }),
                    TextInput::make('phone')->label(GoogleTranslate::trans('Phone Number', session('locale')))
                    ->rules(function ($get) {
                        return [
                            'numeric',
                            Rule::unique('teams', 'phone')
                                ->ignore($get('id')),
                        ];
                    }),
                    TextInput::make('owner')->label(GoogleTranslate::trans('Incorporation Name', session('locale')))->columnSpan(2),
                    TextInput::make('website')->label(GoogleTranslate::trans('Website', session('locale'))),
                    TextInput::make('company_size')->label(GoogleTranslate::trans('Company Size', session('locale')))->numeric(),
                    TextInput::make('gov_registration')->label(GoogleTranslate::trans('Gov. Registration', session('locale'))),
                    TextInput::make('legal_id')->label(GoogleTranslate::trans('Legal / Tax ID', session('locale'))),
                    TextInput::make('exemption_number')->label(GoogleTranslate::trans('Exemption Number', session('locale'))),
                    Select::make('category')
                    ->options([
                        '-' => '-',
                        'easa opens' => 'EASA OPEN',
                        'easa specific' => 'EASA SPECIFIC',
                        'easa certified' => 'EASA CERTIFIED',
                    ])->label(GoogleTranslate::trans('Category', session('locale'))),
                    //country
                    Select::make('countries_id')->label(GoogleTranslate::trans('Country', session('locale')))
                        ->options(countrie::all()->pluck('name','id'))
                        ->reactive()
                        ->afterStateUpdated(fn(callable $set)=>$set('cities_id',null))
                        ->placeholder((new GoogleTranslate(session('locale') ?? 'en'))->translate('Select a Country'))
                        ->searchable()->columnSpan(2),
                    //end
                    //city
                    Select::make('cities_id')->label(GoogleTranslate::trans('City', session('locale')))
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
                    //end
                    TextInput::make('postal_code')->label(GoogleTranslate::trans('Postal Code', session('locale'))),
                    TextInput::make('state')->label(GoogleTranslate::trans('State', session('locale')))->columnSpan(2),
                    TextInput::make('address')->label(GoogleTranslate::trans('Address', session('locale')))->columnSpan(2),
                    CheckBox::make('insurance')->default(1)->label(GoogleTranslate::trans('Insurance', session('locale')))->columnSpan(2),
                    TextInput::make('insurance_amount')->label(GoogleTranslate::trans('Insurance Amount', session('locale'))),
                    TextInput::make('activity')->label(GoogleTranslate::trans('Activity', session('locale'))),
                    TextArea::make('note')->label(GoogleTranslate::trans('Note', session('locale')))->columnSpanFull(),
                ])->columns(4),
                // ...
        
            ]);
    }
    
}