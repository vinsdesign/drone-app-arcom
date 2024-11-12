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
        return static::getModel()::count();
    }

    public static function getNavigationLabel(): string
    {
        return GoogleTranslate::trans('Personnel', session('locale') ?? 'en');
    }
    public static function getModelLabel(): string
    {
        return GoogleTranslate::trans('Personnel', session('locale') ?? 'en');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(GoogleTranslate::trans('Personnel', session('locale') ?? 'en'))
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label(GoogleTranslate::trans('name', session('locale') ?? 'en'))
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('email')
                        ->label(GoogleTranslate::trans('email', session('locale') ?? 'en'))
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
                        ->label(GoogleTranslate::trans('password', session('locale') ?? 'en'))
                        ->password()
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('phone')
                        ->label(GoogleTranslate::trans('phone', session('locale') ?? 'en'))
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
                        // ->label('Country')
                        ->label(GoogleTranslate::trans('Country', session('locale') ?? 'en'))
                        ->options(countrie::all()->pluck('name','id'))
                        ->reactive()
                        ->afterStateUpdated(fn(callable $set)=>$set('cities_id',null))
                        ->placeholder((new GoogleTranslate(session('locale') ?? 'en'))->translate('Select a Country'))
                        ->searchable(),
                    Forms\Components\Select::make('cities_id')->label('City')
                        ->label(GoogleTranslate::trans('City', session('locale') ?? 'en'))
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
                    Forms\Components\TextInput::make('sertif')
                        // ->label('Certificate')
                        ->label(GoogleTranslate::trans('Certificate', session('locale') ?? 'en'))
                        ->maxLength(255),
                    Forms\Components\Hidden::make('teams_id')
                        ->default(auth()->user()->teams()->first()->id ?? null),
                    //role
                    Forms\Components\Select::make('roles')
                        ->label(GoogleTranslate::trans('roles', session('locale') ?? 'en'))
                        ->relationship('roles', 'name')
                        ->multiple()
                        ->preload()
                        ->Options(fn($state)=>
                            Auth()->User()->roles()->where('name','panel_user')->exists()
                            ? DB::table('roles')->where('name', '!=' ,'super_admin')->get()->pluck('name', 'id')
                            : DB::table('roles')->pluck('name', 'id'))
                        ->searchable(),
                        Forms\Components\TextArea::make('address')
                        ->label(GoogleTranslate::trans('address', session('locale') ?? 'en'))
                        ->helperText((new GoogleTranslate(session('locale') ?? 'en'))->translate('Your Specific Address'))
                        ->columnSpanFull()
                        
                    
                ])->columns(3),
                
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                // ->label('Name')
                    ->label(GoogleTranslate::trans('name', session('locale') ?? 'en'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                // ->label('Email')
                    ->label(GoogleTranslate::trans('email', session('locale') ?? 'en'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                // ->label('Phone number')
                    ->label(GoogleTranslate::trans('Phone Number', session('locale') ?? 'en'))
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
                    ->label(GoogleTranslate::trans('roles', session('locale') ?? 'en'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('last_flight')
                    // ->label('Last Flight')
                    ->label(GoogleTranslate::trans('Last Flight', session('locale') ?? 'en'))
                    ->getStateUsing(function ($record) {
                        $lastFlight = $record->fligh()->orderBy('start_date_flight', 'desc')->first();
                        $lastFlightDate = optional($lastFlight)->start_date_flight ? $lastFlight->start_date_flight : '';
                        return "<strong>{$lastFlightDate}</strong>";
                    })
                    ->sortable()
                    ->html(),
                Tables\Columns\TextColumn::make('flight_date')
                    ->label(GoogleTranslate::trans('Total', session('locale') ?? 'en'))
                    ->label('Total')
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
                        return "{$totalFlights} Flight(s) <br> <div style='border: 1px solid #ccc; padding: 3px; display: inline-block; border-radius: 5px; background-color: #D4D4D4; '>
                            <strong>{$totalDuration}</strong> </div>";
                    })
                    ->sortable()
                    ->html(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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
            Section::make('Personel Overview')
            ->schema([
                TextEntry::make('name')->label(GoogleTranslate::trans('name', session('locale') ?? 'en')),
                TextEntry::make('email')->label(GoogleTranslate::trans('email', session('locale') ?? 'en')),
                TextEntry::make('phone')->label(GoogleTranslate::trans('phone', session('locale') ?? 'en')),
                TextEntry::make('countries.name')->label(GoogleTranslate::trans('countries', session('locale') ?? 'en')),
                TextEntry::make('cities.name')->label(GoogleTranslate::trans('cities', session('locale') ?? 'en')),
                TextEntry::make('sertif')->label(GoogleTranslate::trans('certificate', session('locale') ?? 'en')),
                TextEntry::make('roles.name')->label(GoogleTranslate::trans('roles', session('locale') ?? 'en')),
                TextEntry::make('address')->label(GoogleTranslate::trans('address', session('locale') ?? 'en')),
            ])->columns(2)
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
