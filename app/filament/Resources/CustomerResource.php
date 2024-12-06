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
use App\Helpers\TranslationHelper;
use Filament\Notifications\Notification;

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

    public static function getNavigationBadge(): ?string{
        $teamID = Auth()->user()->teams()->first()->id;
        return static::getModel()::Where('teams_id',$teamID)->where('status_visible', '!=', 'archived')->count();
    }

    public static function getNavigationLabel(): string
    {
        return TranslationHelper::translateIfNeeded('Customers');
    }
    public static function getModelLabel(): string
    {
        return TranslationHelper::translateIfNeeded('Customers');
    }
    public static function getNavigationItems(): array
    {
        $user = auth()->user();
        if ($user && !$user->hasRole(['panel_user', 'super_admin'])) {
            return [];
        }
        return parent::getNavigationItems();
    }

    public static function form(Form $form): Form

    {
        $locale = session('locale') ?? 'en'; 
        return $form
        ->schema([
            Forms\Components\Section::make(TranslationHelper::translateIfNeeded('Customers'))
                ->schema([
                    Forms\Components\TextInput::make('name')
                    ->label(TranslationHelper::translateIfNeeded('Name'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone')
                ->label(TranslationHelper::translateIfNeeded('Phone'))
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
                ->label(TranslationHelper::translateIfNeeded('Email'))
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
                ->label(TranslationHelper::translateIfNeeded('Address'))
                    ->required()
                    ->maxLength(255),
    
                Forms\Components\Textarea::make('description')
                ->label(TranslationHelper::translateIfNeeded('Description'))
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
                Tables\Filters\SelectFilter::make('status_visible')
                    ->label('')
                    ->options([
                        'current' => 'Current',
                        'archived' => 'Archived',
                    ])
                    ->default('current'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('Archive')->label(TranslationHelper::translateIfNeeded('Archive'))
                        ->hidden(fn ($record) => $record->status_visible == 'archived')
                                ->action(function ($record) {
                                $record->update(['status_visible' => 'archived']);
                                Notification::make()
                                ->title('Status Updated')
                                ->body("Status successfully changed.")
                                ->success()
                                ->send();
                            })->icon('heroicon-s-archive-box-arrow-down'),
                    Tables\Actions\Action::make('Un-Archive')->label(TranslationHelper::translateIfNeeded(' Un-Archive'))
                        ->hidden(fn ($record) => $record->status_visible == 'current')
                                ->action(function ($record) {
                                $record->update(['status_visible' => 'current']);
                                    Notification::make()
                                    ->title('Status Updated')
                                    ->body("Status successfully changed.")
                                    ->success()
                                    ->send();
                            })->icon('heroicon-s-archive-box'),
                ])
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
            Section::make(TranslationHelper::translateIfNeeded('Customer Overview'))
            ->label(TranslationHelper::translateIfNeeded('Customer Overview'))
            ->schema([
                TextEntry::make('name')
                ->label(TranslationHelper::translateIfNeeded('Name')),
                TextEntry::make('phone')
                ->label(TranslationHelper::translateIfNeeded('Phone')),
                TextEntry::make('email')
                ->label(TranslationHelper::translateIfNeeded('Email')),
                TextEntry::make('address')
                ->label(TranslationHelper::translateIfNeeded('Address')),
                TextEntry::make('description')
                ->label(TranslationHelper::translateIfNeeded('Description')),
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
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'view' => Pages\ViewCustomers::route('/{record}'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}
