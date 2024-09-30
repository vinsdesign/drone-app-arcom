<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KitsResource\Pages;
use App\Filament\Resources\KitsResource\RelationManagers;
use App\Models\Kits;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class KitsResource extends Resource
{
    protected static ?string $model = Kits::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->label('Kit Name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('type')->label('Type')
                    ->options([
                        'battery' => 'Battery',
                        'mix' => 'mix'
                    ])
                    ->required(),
                Forms\Components\Toggle::make('enabled')->label('Enabled')
                    ->required(),
                Forms\Components\BelongsToSelect::make('blocked')->label('Blocked To Drone')
                    ->relationship('drone', 'name')
                    ->nullable()
                    ->searchable()->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->searchable(),
                Tables\Columns\IconColumn::make('enabled')
                    ->boolean(),
                Tables\Columns\TextColumn::make('blocked')
                    ->numeric()
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
            'view' => Pages\ViewKits::route('/{record}'),
            'edit' => Pages\EditKits::route('/{record}/edit'),
        ];
    }
}
