<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KitsResource\Pages;
use App\Filament\Resources\KitsResource\RelationManagers;
use App\Models\battrei;
use App\Models\drone;
use App\Models\equidment;
use App\Models\Kits;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\IconEntry;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Novadaemon\FilamentCombobox\Combobox;
use Filament\Support\Colors\Color;
use App\Helpers\TranslationHelper;

class KitsResource extends Resource
{
    protected static ?string $model = Kits::class;
    // protected static ?string $navigationGroup = 'Inventory';
    // protected static ?string $navigationLabel = 'Kit';

    protected static ?string $navigationIcon = 'heroicon-c-briefcase';
    public static ?string $tenantOwnershipRelationshipName = 'teams';
    protected static bool $isLazy = false;

    public static function getNavigationBadge(): ?string{
        $teamID = Auth()->user()->teams()->first()->id;
        return static::getModel()::Where('teams_id',$teamID)->count();
    }

    public static function getNavigationLabel(): string
    {
        return TranslationHelper::translateIfNeeded('Kit');
    }
    public static function getModelLabel(): string
    {
        return TranslationHelper::translateIfNeeded('Kit');
    }

    public static function form(Form $form): Form
    {
        $currentTeamId = auth()->user()->teams()->first()->id;
        return $form
            ->schema([
                Forms\Components\Hidden::make('teams_id')
                    ->default(auth()->user()->teams()->first()->id ?? null),
                Forms\Components\TextInput::make('name')
                ->label(TranslationHelper::translateIfNeeded('Kit Name'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('type')
                ->label(TranslationHelper::translateIfNeeded('Type'))
                    ->options([
                        'battery' => 'Battery',
                        'mix' => 'mix'
                    ])
                    ->required()
                    ->reactive(),
                Forms\Components\Toggle::make('enabled')
                ->label(TranslationHelper::translateIfNeeded('Enabled'))
                    ->required(),
                Forms\Components\Select::make('drone_id')
                ->label(TranslationHelper::translateIfNeeded('Blocked To Drone'))    
                    ->options(function (callable $get) use ($currentTeamId) {
                        return drone::where('teams_id', $currentTeamId)->pluck('name', 'id');
                    })
                    ->nullable()
                    ->searchable()
                    ->columnSpanFull(),

                Forms\Components\Select::make('Batteries')
                ->label(TranslationHelper::translateIfNeeded('Batteries'))
                    ->multiple()
                    ->relationship('battrei', 'name')
                    ->options(
                        battrei::where('teams_id', auth()->user()->teams()->first()->id)
                        ->whereDoesntHave('kits', function ($query){
                            $query->whereNotNull('battrei_id');
                            })
                            ->pluck('name', 'id')
                    )
                    ->visible(fn (callable $get) => $get('type') === 'battery' || $get('type') === 'mix')
                    ->required(fn (callable $get) => $get('type') === 'battery' || $get('type') === 'mix')
                    ->searchable()
                    ->saveRelationshipsUsing(function ($component, $state) {
                        $component->getRecord()->battrei()->sync($state);
                    }),

                Forms\Components\Select::make('Equipments')
                ->label(TranslationHelper::translateIfNeeded('Equipments'))
                    ->multiple()
                    ->relationship('equidment', 'name')
                    ->options(
                        equidment::where('teams_id', auth()->user()->teams()->first()->id)
                            ->whereDoesntHave('kits', function ($query){
                                $query->whereNotNull('equidment_id');
                            })
                            // ->pluck('name', 'id')
                            ->get()
                            ->mapWithKeys(function ($equidment) {
                                return [$equidment->id => "{$equidment->name} [{$equidment->type}]"];
                            })
                    )
                    ->visible(fn (callable $get) => $get('type') === 'mix')
                    ->required(fn (callable $get) => $get('type') === 'mix')
                    ->searchable()
                    ->saveRelationshipsUsing(function ($component, $state) {
                        $component->getRecord()->equidment()->sync($state);
                    }),
             ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            Tables\Columns\TextColumn::make('name')
                ->label(TranslationHelper::translateIfNeeded('Name'))
                ->searchable(),
            Tables\Columns\TextColumn::make('type')
                ->label(TranslationHelper::translateIfNeeded('Type'))
                ->searchable(),
            Tables\Columns\IconColumn::make('enabled')
                ->label(TranslationHelper::translateIfNeeded('Enabled'))
                ->boolean(),
            Tables\Columns\TextColumn::make('drone.name')
                ->label(TranslationHelper::translateIfNeeded('Blocked To Drone'))
                ->numeric()
                ->url(fn($record) => $record->drone_id ? route('filament.admin.resources.drones.view', [
                    'tenant' => Auth()->user()->teams()->first()->id,
                    'record' => $record->drone_id,
                ]) : null)->color(Color::Blue)
                ->sortable()
                ->placeholder(TranslationHelper::translateIfNeeded('No drone selected')),
            Tables\Columns\TextColumn::make('battrei.name')
                ->label(TranslationHelper::translateIfNeeded('Battery'))
                ->numeric()
                // ->url(fn($record) => $record->battrei->first() ? route('filament.admin.resources.battreis.view', [
                //     'tenant' => Auth()->user()->teams()->first()->id,
                //     'record' => $record->battrei->first()->id,
                // ]) : null)->color(Color::Blue)
                ->formatStateUsing(function ($record) {
                    return $record->battrei->map(function ($battrei) {
                        return "<a href='" . route('filament.admin.resources.battreis.view', [
                            'tenant' => auth()->user()->teams()->first()->id,
                            'record' => $battrei->id,
                        ]) . "' style='color: #3b82f6; text-decoration: underline; font-size: 0.875rem;'>{$battrei->name}</a>";
                    })->implode(', ');
                })
                ->html()
                ->sortable(),
            Tables\Columns\TextColumn::make('equidment.name')
                ->label(TranslationHelper::translateIfNeeded('Equipment'))
                ->numeric()
                // ->url(fn($record) => $record->equidment->first() ? route('filament.admin.resources.equidments.view', [
                //     'tenant' => Auth()->user()->teams()->first()->id,
                //     'record' => $record->equidment->first()->id,
                // ]) : null)
                // ->color(Color::Blue)
                ->formatStateUsing(function ($record) {
                    return $record->equidment->map(function ($equidment) {
                        return "<a href='" . route('filament.admin.resources.equidments.view', [
                            'tenant' => auth()->user()->teams()->first()->id,
                            'record' => $equidment->id,
                        ]) . "' style='color: #3b82f6; text-decoration: underline; font-size: 0.875rem;'>{$equidment->name}</a>";
                    })->implode(', ');
                })
                ->html()
                ->sortable(),
            Tables\Columns\TextColumn::make('created_at')
                ->label(TranslationHelper::translateIfNeeded('Created at'))
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            Tables\Columns\TextColumn::make('updated_at')
                ->label(TranslationHelper::translateIfNeeded('Updated at'))
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ])        
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
        ->schema([
            TextEntry::make('name')
                ->label(TranslationHelper::translateIfNeeded('Name')),
            TextEntry::make('type')
                ->label(TranslationHelper::translateIfNeeded('Type')),
            IconEntry::make('enabled')
                ->boolean()
                ->label(TranslationHelper::translateIfNeeded('Enabled')),
            TextEntry::make('drone.name')
                ->label(TranslationHelper::translateIfNeeded('Blocked To Drone')),
            TextEntry::make('battrei.name')
                ->label(TranslationHelper::translateIfNeeded('Battery')),
            TextEntry::make('equidment.name')
                ->label(TranslationHelper::translateIfNeeded('Equipment')),
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
            'index' => Pages\ListKits::route('/'),
            'create' => Pages\CreateKits::route('/create'),
            //'view' => Pages\ViewKits::route('/{record}'),
            'edit' => Pages\EditKits::route('/{record}/edit'),
        ];
    }
}
