<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
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

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationLabel = 'Personnel';
    protected static ?string $navigationIcon = 'heroicon-s-user-group';
    protected static ?string $modelLabel = 'Personnel';
    public static ?string $tenantOwnershipRelationshipName = 'teams';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Personel')
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
                    Forms\Components\TextInput::make('country')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('lenguage')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('sertif')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('timezone')
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
                        ->searchable()->columnSpanFull()
                        
                    
                ])->columns(3),
                
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('country')
                    ->searchable(),
                Tables\Columns\TextColumn::make('lenguage')
                    ->label('Language')
                    ->searchable(),
                Tables\Columns\TextColumn::make('sertif')
                    ->label('Certificate')
                    ->searchable(),
                Tables\Columns\TextColumn::make('timezone')
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
            TextEntry::make('name')->label('Name'),
            TextEntry::make('email')->label('Email'),
            TextEntry::make('phone')->label('Phone'),
            TextEntry::make('country')->label('Country'),
            TextEntry::make('lenguage')->label('Language'),
            TextEntry::make('sertif')->label('Certificate'),
            TextEntry::make('timezone')->label('Timezone'),
            TextEntry::make('roles.name')->label('Role Type'),
        ])->columns(2);
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
