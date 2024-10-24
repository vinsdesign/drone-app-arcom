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
class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationLabel = 'Personnel';
    protected static ?string $navigationIcon = 'heroicon-s-user-group';
    protected static ?string $modelLabel = 'Personnel';
    public static ?int $navigationSort = 2;
    public static ?string $navigationGroup = ' ';
    public static ?string $tenantOwnershipRelationshipName = 'teams';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Personnel')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('email')
                        ->email()
                        ->required()
                        ->unique(User::class, 'email') // Validasi unique
                        ->rules(['unique:users,email'])
                        ->maxLength(255)->columnSpan(2),
                    Forms\Components\TextInput::make('password')
                        ->password()
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('phone')
                        ->tel()
                        ->unique(User::class, 'phone') // Validasi unique
                        ->rules(['unique:users,phone'])
                        ->numeric(),
                    Forms\Components\Select::make('countries_id')->label('Country')
                        ->options(countrie::all()->pluck('name','id'))
                        ->reactive()
                        ->afterStateUpdated(fn(callable $set)=>$set('cities_id',null))
                        ->placeholder('Select a Country')
                        ->searchable(),
                    Forms\Components\Select::make('Cities_id')->label('City')
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
                    Forms\Components\TextInput::make('sertif')
                        ->label('Certificate')
                        ->maxLength(255),
                    Forms\Components\Hidden::make('teams_id')
                        ->default(auth()->user()->teams()->first()->id ?? null),
                    //role
                    Forms\Components\Select::make('roles')
                        ->relationship('roles', 'name')
                        ->multiple()
                        ->preload()
                        ->Options(fn($state)=>
                            Auth()->User()->roles()->where('name','panel_user')->exists()
                            ? DB::table('roles')->where('name', '!=' ,'super_admin')->get()->pluck('name', 'id')
                            : DB::table('roles')->pluck('name', 'id'))
                        ->searchable(),
                        Forms\Components\TextArea::make('address')
                        ->helperText('Your Specific Address')
                        ->columnSpanFull()
                        
                    
                ])->columns(3),
                
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')->label('Email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')->label('Phone number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('countries.name')->label('Country')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cities.name')->label('City')
                    ->label('City')
                    ->searchable(),
                Tables\Columns\TextColumn::make('sertif')
                    ->label('Certificate')
                    ->searchable(),
                Tables\Columns\TextColumn::make('address')->label('Address')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('image'),
                Tables\Columns\TextColumn::make('roles.name')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                // Tables\Actions\EditAction::make(),
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
                TextEntry::make('name')->label('Name'),
                TextEntry::make('email')->label('Email'),
                TextEntry::make('phone')->label('Phone'),
                TextEntry::make('countries.name')->label('Country'),
                TextEntry::make('cities.name')->label('Language'),
                TextEntry::make('sertif')->label('Certificate'),
                TextEntry::make('roles.name')->label('Role Type'),
                TextEntry::make('address')->label('Address'),
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
            // 'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
