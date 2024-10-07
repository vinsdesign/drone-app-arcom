<?php
namespace App\Filament\Pages\Tenancy;
 
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextArea;
use Filament\Forms\Form;
use Filament\Pages\Tenancy\EditTenantProfile;
 
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
                    TextInput::make('name')->label('Organization Name')->columnSpan(2),
                    TextInput::make('email')->email()->label('Email Address'),
                    TextInput::make('phone')->label('Phone Number'),
                    TextInput::make('owner')->label('Incorporation Name')->columnSpan(2),
                    TextInput::make('website')->label('Website'),
                    TextInput::make('company_size')->label('Company Size')->numeric(),
                    TextInput::make('gov_registration')->label('Gov. Registration'),
                    TextInput::make('legal_id')->label('Legal / Tax ID'),
                    TextInput::make('exemption_number')->label('Exemption Number'),
                    Select::make('category')
                    ->options([
                        '-' => '-',
                        'easa opens' => 'EASA OPEN',
                        'easa specific' => 'EASA SPECIFIC',
                        'easa certified' => 'EASA CERTIFIED',
                    ])->label('Category'),
                    TextInput::make('address')->label('Address')->columnSpan(2),
                    TextInput::make('city')->label('City')->columnSpan(2),
                    TextInput::make('state')->label('State')->columnSpan(2),
                    TextInput::make('postal_code')->label('Postal Code'),
                    TextInput::make('country')->label('Country'),
                    CheckBox::make('insurance')->default(1)->label('Insurance')->columnSpan(2),
                    TextInput::make('insurance_amount')->label('Insurance Amount'),
                    TextInput::make('activity')->label('Activity'),
                    TextArea::make('note')->label('Note')->columnSpanFull(),
                ])->columns(4),
                
                // ...
            ]);
    }
}