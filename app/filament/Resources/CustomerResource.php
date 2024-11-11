<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Filament\Resources\CustomerResource\RelationManagers;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Layout\View;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Validation\Rule;
use Illuminate\Support\HtmlString;
use Stichoza\GoogleTranslate\GoogleTranslate;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;
    // protected static ?string $navigationLabel = 'Customers';
    // protected static ?string $navigationGroup = 'Masters';
    protected static ?string $navigationIcon = 'heroicon-s-user';
    public static ?string $tenantOwnershipRelationshipName = 'teams';
    public static ?int $navigationSort = 1;
    public static ?string $navigationGroup = ' ';
    protected static bool $isLazy = false;

    public static function getNavigationLabel(): string
    {
        return GoogleTranslate::trans('Customers', session('locale') ?? 'en');
    }
    public static function getModelLabel(): string
    {
        return GoogleTranslate::trans('Customers', session('locale') ?? 'en');
    }

    public static function form(Form $form): Form

    {
        $locale = session('locale') ?? 'en'; 
        return $form
        ->schema([
            Forms\Components\Section::make(GoogleTranslate::trans('Customer', session('locale') ?? 'en'))
                ->label((new GoogleTranslate($locale))->translate('Customer'))
                ->schema([
                    Forms\Components\TextInput::make('name')
                    ->label(GoogleTranslate::trans('name', session('locale') ?? 'en'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone')
                    ->label(GoogleTranslate::trans('phone', session('locale') ?? 'en'))
                    ->tel()
                    ->required()
                    ->rules(function ($get) {
                        return [
                            'required',
                            'numeric',
                            Rule::unique('customers', 'phone')
                                ->ignore($get('id')),
                        ];
                    }),
                Forms\Components\TextInput::make('email')
                    ->label(GoogleTranslate::trans('email', session('locale') ?? 'en'))
                    ->email()
                    ->required()
                    ->rules(function ($get) {
                        return [
                            'required',
                            'email',
                            Rule::unique('customers', 'email')
                                ->ignore($get('id')),
                        ];
                    })
                    ->maxLength(255),
                Forms\Components\TextInput::make('address')
                    ->label(GoogleTranslate::trans('address', session('locale') ?? 'en'))
                    ->required()
                    ->maxLength(255),
    
                Forms\Components\Textarea::make('description')
                    ->label(GoogleTranslate::trans('description', session('locale') ?? 'en'))
                    ->maxLength(255)->columnSpanFull(),
                Forms\Components\Hidden::make('teams_id')
                ->default(auth()->user()->teams()->first()->id ?? null),
                ])->columns(2),

        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            
            ->columns([
                Tables\Columns\TextColumn::make('id')
                ->formatStateUsing(fn ($record) => view('component.table.table-customer', ['record' => $record]))
                ->extraAttributes(['class' => 'w-full'])
                ->disableClick(),
    
                    Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                    Tables\Columns\TextColumn::make('phone')
                        ->searchable()
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: true),

                    Tables\Columns\TextColumn::make('email')
                        ->searchable()
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: true),


                        //     Tables\Columns\TextColumn::make('name')
                        //     ->searchable(),
                        // Tables\Columns\TextColumn::make('phone')
                        //     ->searchable(),
                        // Tables\Columns\TextColumn::make('email')
                        //     ->searchable(),
                        // Tables\Columns\TextColumn::make('address')
                        //     ->searchable(),
                        // Tables\Columns\TextColumn::make('description')
                        //     ->searchable(),
                        // Tables\Columns\TextColumn::make('created_at')
                        //     ->dateTime()
                        //     ->sortable()
                        //     ->toggleable(isToggledHiddenByDefault: true),
                        // Tables\Columns\TextColumn::make('updated_at')
                        //     ->dateTime()
                        //     ->sortable()
                        //     ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
    //untuk tenancy

    //end te

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
        ->schema([
            Section::make('Customer Overview')
            ->label(GoogleTranslate::trans('Customer Overview', session('locale') ?? 'en'))
            ->schema([
                TextEntry::make('name')->label(GoogleTranslate::trans('name', session('locale') ?? 'en')),
                TextEntry::make('phone')->label(GoogleTranslate::trans('phone', session('locale') ?? 'en')),
                TextEntry::make('email')->label(GoogleTranslate::trans('email', session('locale') ?? 'en')),
                TextEntry::make('address')->label(GoogleTranslate::trans('address', session('locale') ?? 'en')),
                TextEntry::make('description')->label(GoogleTranslate::trans('description', session('locale') ?? 'en')),
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
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}
