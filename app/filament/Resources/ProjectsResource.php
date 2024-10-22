<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectsResource\Pages;
use App\Filament\Resources\ProjectsResource\RelationManagers;
use App\Models\currencie;
use App\Models\customer;
use App\Models\Projects;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Support\Colors\Color;

class ProjectsResource extends Resource
{
    protected static ?string $model = Projects::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    public static ?string $tenantOwnershipRelationshipName = 'teams';
    public static ?int $navigationSort = 4;
    public static ?string $navigationGroup = ' ';

    public static function form(Form $form): Form
    {
        $currentTeamId = auth()->user()->current_teams_id;
        return $form
            ->schema([
                Forms\Components\Section::make('Projects')
                    ->schema([
                        Forms\Components\Hidden::make('teams_id')
                        ->default(auth()->user()->teams()->first()->id ?? null),
                        Forms\Components\TextInput::make('case')->label('Case')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('revenue')->label('Revenue')
                            ->required()
                            ->numeric(),
                        Forms\Components\Select::make('currencies_id')->label('Currency')
                        ->options(currencie::all()->mapWithKeys(function ($currency) {
                            return [$currency->id => "{$currency->name} - {$currency->iso}"];}))
                            ->searchable()
                            ->required(),
                        Forms\Components\Select::make('customers_id')->label('Customer Name') 
                            ->options(customer::where('teams_id', auth()->user()->teams()->first()->id)
                            ->pluck('name', 'id')
                            )->searchable()
                            ->placeholder('Select an Customer')
                            ->required(),
                        Forms\Components\TextArea::make('description')->label('Description')
                            ->required()
                            ->maxLength(255)->columnSpanFull(),

                ])->columns(2),
               
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('case')
                    ->searchable(),
                Tables\Columns\TextColumn::make('revenue')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('currencies.iso')
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->searchable(),
                Tables\Columns\TextColumn::make('customers.name')
                    ->numeric()
                    ->url(fn($record) => $record->customers_id ? route('filament.admin.resources.customers.index', [
                        'tenant' => Auth()->user()->teams()->first()->id,
                        'record' => $record->customers_id,
                    ]):null)->color(Color::Blue)
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
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
            TextEntry::make('case')->label('Case'),
            TextEntry::make('revenue')->label('Revenue'),
            TextEntry::make('currencies.iso')->label('Currency'),
            TextEntry::make('customers.name')->label('Customers')
            ->url(fn($record) => $record->customers_id ? route('filament.admin.resources.customers.index', [
                'tenant' => Auth()->user()->teams()->first()->id,
                'record' => $record->customers_id,
            ]):null)->color(Color::Blue),
            TextEntry::make('description')->label('Description'),
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
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProjects::route('/create'),
            'edit' => Pages\EditProjects::route('/{record}/edit'),
        ];
    }
}
