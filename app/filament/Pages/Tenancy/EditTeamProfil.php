<?php
namespace App\Filament\Pages\Tenancy;
 
use App\Models\citie;
use App\Models\countrie;
use App\Models\team;
use Auth;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextArea;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Tenancy\EditTenantProfile;
use Illuminate\Validation\Rule;
use App\Helpers\TranslationHelper;
 
class EditTeamProfil extends EditTenantProfile
{
    public static function getLabel(): string
    {
        return TranslationHelper::translateIfNeeded('Team Profile');
    }
 
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(TranslationHelper::translateIfNeeded('Organization Information'))
                ->description('')
                ->schema([
                    FileUpload::make('avatar_url')->label(TranslationHelper::translateIfNeeded('Your Avatar'))
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif'])
                    ->helperText(TranslationHelper::translateIfNeeded('Please choose image type jpg/jpeg/png')),
                    TextInput::make('name')->label(TranslationHelper::translateIfNeeded('Organization Name'))->columnSpan(2),
                    TextInput::make('email')->email()->label(TranslationHelper::translateIfNeeded('Email Address'))
                    ->rules(function ($get) {
                        return [
                            'email',
                            Rule::unique('teams', 'email')
                                ->ignore($get('id')),
                        ];
                    }),
                    TextInput::make('phone')->label(TranslationHelper::translateIfNeeded('Phone Number'))
                    ->rules(function ($get) {
                        return [
                            'numeric',
                            Rule::unique('teams', 'phone')
                                ->ignore($get('id')),
                        ];
                    }),
                    TextInput::make('owner')->label(TranslationHelper::translateIfNeeded('Incorporation Name'))->columnSpan(2),
                    TextInput::make('website')->label(TranslationHelper::translateIfNeeded('Website')),
                    TextInput::make('company_size')->label(TranslationHelper::translateIfNeeded('Company Size'))->numeric(),
                    TextInput::make('gov_registration')->label(TranslationHelper::translateIfNeeded('Gov. Registration')),
                    TextInput::make('legal_id')->label(TranslationHelper::translateIfNeeded('Legal / Tax ID')),
                    TextInput::make('exemption_number')->label(TranslationHelper::translateIfNeeded('Exemption Number')),
                    Select::make('category')
                    ->options([
                        '-' => '-',
                        'easa opens' => 'EASA OPEN',
                        'easa specific' => 'EASA SPECIFIC',
                        'easa certified' => 'EASA CERTIFIED',
                    ])->label(TranslationHelper::translateIfNeeded('Category')),
                    //country
                    Select::make('countries_id')->label(TranslationHelper::translateIfNeeded('Country'))
                        ->options(countrie::all()->pluck('name','id'))
                        ->reactive()
                        ->afterStateUpdated(fn(callable $set)=>$set('cities_id',null))
                        ->placeholder(TranslationHelper::translateIfNeeded('Select a Country'))
                        ->searchable()->columnSpan(2),
                    //end
                    //city
                    Select::make('cities_id')->label(TranslationHelper::translateIfNeeded('City'))
                    ->options(function ($get) {
                            $countryId = $get('countries_id');
                            if ($countryId) {
                                return citie::where('country_id', $countryId)->pluck('name', 'id');
                            }
                            return citie::pluck('name', 'id');
                        })
                    ->searchable()
                    ->reactive()
                    ->placeholder(TranslationHelper::translateIfNeeded('Select a City'))
                    ->disabled(fn ($get) => !$get('countries_id')),
                    //end
                    TextInput::make('postal_code')->label(TranslationHelper::translateIfNeeded('Postal Code')),
                    TextInput::make('state')->label(TranslationHelper::translateIfNeeded('State'))->columnSpan(2),
                    TextInput::make('address')->label(TranslationHelper::translateIfNeeded('Address'))->columnSpan(2),
                    CheckBox::make('insurance')->default(1)->label(TranslationHelper::translateIfNeeded('Insurance'))->columnSpan(2),
                    TextInput::make('insurance_amount')->label(TranslationHelper::translateIfNeeded('Insurance Amount')),
                    TextInput::make('activity')->label(TranslationHelper::translateIfNeeded('Activity')),
                    TextArea::make('note')->label(TranslationHelper::translateIfNeeded('Note'))->columnSpanFull(),
                ])->columns(4),
                // ...
        
            ]);
    }
        public function mount():void
        {
            parent::mount();
            $allowedRoles = ['super_admin', 'panel_user'];
            $userRoles = Auth::user()->roles()->pluck('name')->toArray();

            if (!array_intersect($allowedRoles, $userRoles)) {
                Notification::make()
                    ->title('Access Denied')
                    ->danger()
                    ->body('You do not have permission to access this page.')
                    ->send();
                abort(403, 'Unauthorized');
            }
        }
    
}