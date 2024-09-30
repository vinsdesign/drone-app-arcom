<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EquidmentResource\Pages;
use App\Filament\Resources\EquidmentResource\RelationManagers;
use App\Models\Equidment;
use Filament\Forms;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EquidmentResource extends Resource
{
    protected static ?string $model = Equidment::class;
    protected static ?string $navigationLabel = 'Equidment z' ;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static?string $navigationGroup = 'Inventory';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Wizard::make([
                    Forms\Components\Wizard\Step::make('Overview')
                        ->schema([
                        Forms\Components\TextInput::make('name')->label('Name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('model')->label('model')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\select::make('status')->label('Status')
                            ->options([
                                'airworthy' => 'Airworthy',
                                'maintenance' => 'Maintenance',
                                'retired' => 'Retired',
                            ])
                            ->required(),
                        Forms\Components\Select::make('inventory_asset')->label('Inventory / Asset')
                            ->options([
                                'asset'=> 'Assets',
                                'inventory'=> 'Inventory',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('serial')->label('Serial')
                            ->required()->columnSpan(2)
                            ->numeric(),
                        Forms\Components\TextInput::make('type')->label('Type')
                            ->required()->columnSpan(2)
                            ->maxLength(255),
                        Forms\Components\BelongsToSelect::make('for_drone')->label('Blocked To Drone')
                            ->relationship('drone', 'name')->columnSpanFull()
                            ->searchable()->nullable(),
                    ])->columns(4),
                    //form ke dua
                    Forms\Components\Wizard\Step::make('Extra Information')
                        ->schema([
                            Forms\Components\TextInput::make('owner_id')->label('Owner')
                            ->numeric(),
                        Forms\Components\DatePicker::make('purchase_date')->label('Purchase date')
                            ->required(),
                        Forms\Components\TextInput::make('insurable_value')->label('Insurable Value')
                            ->required()
                            ->numeric(),
                        Forms\Components\TextInput::make('firmware_v')->label('Firmware Version ')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('hardware_v')->label('Hardware Version')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Checkbox::make('is_loaner')->label('Loaner Equidment')
                            ->required(),
                        Forms\Components\TextArea::make('description')->label('Description')
                            ->required()
                            ->maxLength(255),
                        ])->columns(3),
                        //end wizard2
                ])->columnSpanFull(),
                //end wizard

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('model')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\IconColumn::make('inventory_asset')
                    ->boolean(),
                Tables\Columns\TextColumn::make('serial')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('for_drone')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('owner_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('purchase_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('insurable_value')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('firmware_v')
                    ->searchable(),
                Tables\Columns\TextColumn::make('hardware_v')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_loaner')
                    ->boolean(),
                Tables\Columns\TextColumn::make('description')
                    ->searchable(),
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
            'index' => Pages\ListEquidments::route('/'),
            'create' => Pages\CreateEquidment::route('/create'),
            'view' => Pages\ViewEquidment::route('/{record}'),
            'edit' => Pages\EditEquidment::route('/{record}/edit'),
        ];
    }
}
