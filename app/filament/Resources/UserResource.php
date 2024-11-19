<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\citie;
use App\Models\countrie;
use App\Models\User;
use App\Models\model_has_role;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Support\Facades\DB;
use Filament\Infolists\Components\Section;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Stichoza\GoogleTranslate\GoogleTranslate;
use Filament\Infolists\Components\View as InfolistView;
use App\Helpers\TranslationHelper;
class UserResource extends Resource
{
    protected static ?string $model = User::class;
    // protected static ?string $navigationLabel = 'Personnel';
    protected static ?string $navigationIcon = 'heroicon-s-user-group';
    // protected static ?string $modelLabel = 'Personnel';
    public static ?int $navigationSort = 2;
    public static ?string $navigationGroup = ' ';
    public static ?string $tenantOwnershipRelationshipName = 'teams';
    protected static bool $isLazy = false;

    public static function getNavigationBadge(): ?string{
        $teamId = Auth()->user()->teams()->first()->id;
        return static::getModel()::whereHas('teams', function ($query) use ($teamId) {
            $query->where('team_id', $teamId);
        })->count();
    }

    public static function getNavigationLabel(): string
    {
        return TranslationHelper::translateIfNeeded('Personnel');
    }
    public static function getModelLabel(): string
    {
        return TranslationHelper::translateIfNeeded('Personnel');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(TranslationHelper::translateIfNeeded('Personnel'))
                ->schema([
                    Forms\Components\TextInput::make('name')
                    ->label(TranslationHelper::translateIfNeeded('name'))    
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('email')
                    ->label(TranslationHelper::translateIfNeeded('email'))    
                        ->email()
                        ->required()
                        ->rules(function ($get) {
                            return [
                                'required',
                                'email',
                                Rule::unique('users', 'email')
                                    ->ignore($get('id')),
                            ];
                        })
                        ->maxLength(255)->columnSpan(2),
                    Forms\Components\TextInput::make('password')
                    ->label(TranslationHelper::translateIfNeeded('password'))    
                        ->password()
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('phone')
                    ->label(TranslationHelper::translateIfNeeded('phone'))    
                        ->tel()
                        ->rules(function ($get) {
                            return [
                                'required',
                                'numeric',
                                Rule::unique('users', 'phone')
                                    ->ignore($get('id')),
                            ];
                        })
                        ->numeric(),
                    Forms\Components\Select::make('countries_id')
                    ->label(TranslationHelper::translateIfNeeded('Country'))    
                        ->options(countrie::all()->pluck('name','id'))
                        ->reactive()
                        ->afterStateUpdated(fn(callable $set)=>$set('cities_id',null))
                        ->placeholder(TranslationHelper::translateIfNeeded('Select a Country'))
                        ->searchable(),
                    Forms\Components\Select::make('cities_id')
                    ->label(TranslationHelper::translateIfNeeded('City'))    
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
                    Forms\Components\TextInput::make('sertif')
                        ->label(TranslationHelper::translateIfNeeded('Certificate'))
                        ->maxLength(255),
                    Forms\Components\Hidden::make('teams_id')
                        ->default(auth()->user()->teams()->first()->id ?? null),
                    //role
                    Forms\Components\Select::make('roles')
                    ->label(TranslationHelper::translateIfNeeded('roles'))    
                        ->relationship('roles', 'name')
                        ->multiple()
                        ->preload()
                        ->Options(fn($state)=>
                            Auth()->User()->roles()->where('name','panel_user')->exists()
                            ? DB::table('roles')->where('name', '!=' ,'super_admin')->get()->pluck('name', 'id')
                            : DB::table('roles')->pluck('name', 'id'))
                        ->searchable(),
                        Forms\Components\TextArea::make('address')
                        ->label(TranslationHelper::translateIfNeeded('address'))
                        ->helperText(TranslationHelper::translateIfNeeded('Your Specific Address'))
                        ->columnSpanFull()
                        
                    
                ])->columns(3),
                
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                ->label(TranslationHelper::translateIfNeeded('Name'))    
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                ->label(TranslationHelper::translateIfNeeded('Email'))    
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                ->label(TranslationHelper::translateIfNeeded('Phone Number'))    
                    ->searchable(),
                // Tables\Columns\TextColumn::make('countries.name')->label('Country')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('cities.name')->label('City')
                //     ->label('City')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('sertif')
                //     ->label('Certificate')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('address')->label('Address')
                //     ->searchable(),
                // Tables\Columns\ImageColumn::make('image'),
                Tables\Columns\TextColumn::make('roles.name')
                ->label(TranslationHelper::translateIfNeeded('roles'))    
                    ->searchable(),
                Tables\Columns\TextColumn::make('last_flight')
                ->label(TranslationHelper::translateIfNeeded('Last Flight'))    
                    ->getStateUsing(function ($record) {
                        $lastFlight = $record->fligh()->orderBy('start_date_flight', 'desc')->first();
                        $lastFlightDate = optional($lastFlight)->start_date_flight ? $lastFlight->start_date_flight : '';
                        return "<strong>{$lastFlightDate}</strong>";
                    })
                    ->sortable()
                    ->html(),
                Tables\Columns\TextColumn::make('flight_date')
                ->label(TranslationHelper::translateIfNeeded('Total'))    
                    ->getStateUsing(function ($record) {
                        $flights = $record->fligh;
                        $totalFlights = $record->fligh()->count();

                        $totalSeconds = 0;
                        foreach ($flights as $flight) {
                            $start = $flight->start_date_flight;
                            $end = $flight->end_date_flight;
                    
                            if ($start && $end) {
                                $totalSeconds += Carbon::parse($start)->diffInSeconds(Carbon::parse($end));
                            }
                        }
                    
                        $hours = floor($totalSeconds / 3600);
                        $minutes = floor(($totalSeconds % 3600) / 60);
                        $seconds = $totalSeconds % 60;
                        $totalDuration = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
                        $TranslateText = TranslationHelper::translateIfNeeded('Flights');
                        return "{$totalFlights} {$TranslateText} <br> <div style='border: 1px solid #ccc; padding: 3px; display: inline-block; border-radius: 5px; background-color: #D4D4D4; '>
                            <strong>{$totalDuration}</strong> </div>";
                    })
                    ->sortable()
                    ->html(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('views')  ->action(function ($record) {
                        session(['personnel_id' => $record->id]);
                        return redirect()->route('flight-personnel', ['personnel_id' => $record->id]);
                    })->label(TranslationHelper::translateIfNeeded('View'))->icon('heroicon-s-eye'),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    //infolist users
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
        
        ->schema([
            Section::make(TranslationHelper::translateIfNeeded('Personel Overview'))
                ->schema([
                    TextEntry::make('name')->label(TranslationHelper::translateIfNeeded('name')),
                    TextEntry::make('email')->label(TranslationHelper::translateIfNeeded('email')),
                    TextEntry::make('phone')->label(TranslationHelper::translateIfNeeded('phone')),
                    TextEntry::make('countries.name')->label(TranslationHelper::translateIfNeeded('countries')),
                    TextEntry::make('cities.name')->label(TranslationHelper::translateIfNeeded('cities')),
                    TextEntry::make('sertif')->label(TranslationHelper::translateIfNeeded('certificate')),
                    TextEntry::make('roles.name')->label(TranslationHelper::translateIfNeeded('roles')),
                    TextEntry::make('address')->label(TranslationHelper::translateIfNeeded('address')),
                ])->columns(2),
                 Section::make('')
                    ->schema([
                        InfolistView::make('component.flight-personnel')
                    ])
                ]);
     
    }
    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
