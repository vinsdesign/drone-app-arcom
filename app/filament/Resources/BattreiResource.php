<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BattreiResource\Pages;
use App\Filament\Resources\BattreiResource\RelationManagers;
use App\Models\Battrei;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;


class BattreiResource extends Resource
{
    protected static ?string $model = Battrei::class;
    protected static ?string $navigationGroup = 'Inventory';
    protected static ?string $navigationLabel = 'Battery';

    protected static ?string $navigationIcon = 'heroicon-s-battery-100';

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
                        Forms\Components\TextInput::make('model')->label('Model')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('status')->label('Status')
                            ->options([
                                'airworthy' => 'Airworthy',
                                'maintenance' => 'Maintenance',
                                'retired' => 'Retired'
                            ])
                            ->required(),
                        Forms\Components\Select::make('asset_inventory')->label('Inventory / Asset')
                            ->options([
                                'asset' => 'Asset',
                                'inventory' => 'Inventory',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('serial_P')->label('Serial #(Printed)')
                            ->required()
                            ->numeric()->columnSpan(2),
                        Forms\Components\TextInput::make('serial_I')->label('Serial #(Internal)')
                            ->required()
                            ->numeric()->columnSpan(2),
                        Forms\Components\BelongsToSelect::make('for_drone')->label('For Drone (Optional)')
                            ->relationship('drone', 'name')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('cellCount')->label('Cell Count')
                            ->required()
                            ->numeric(),
                        Forms\Components\TextInput::make('nominal_voltage')->label('Nominal Voltage (V)')
                            ->required()
                            ->numeric(),
                        Forms\Components\TextInput::make('capacity')->label('Capacity (mAh)')
                            ->required()
                            ->numeric(),
                        Forms\Components\TextInput::make('initial_Cycle_count')->label('Initial Cycle Count')
                            ->required()
                            ->numeric(),
                        Forms\Components\TextInput::make('life_span')->label('Life Span')
                            ->required()
                            ->numeric(),
                        Forms\Components\TextInput::make('flaight_count')->label('Flight Count')
                            ->required()
                            ->numeric()->columnSpan(1),
                        ])->columns(4),
                        //end wizard 1
                    Forms\Components\Wizard\Step::make('Extra Information')
                        ->schema([
                        Forms\Components\Select::make('users_id')->label('Owner')
                            ->relationship('users', 'name')
                            ->required(),
                        Forms\Components\DatePicker::make('purchase_date')->label('Purchase date')
                            ->required(),
                        Forms\Components\TextInput::make('insurable_value')->label('Insurable Value')
                            ->required()
                            ->numeric(),
                        Forms\Components\TextInput::make('wight')->label('Wight')
                            ->required()
                            ->numeric(),
                        Forms\Components\TextInput::make('firmware_version')->label('Firmware Version')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('hardware_version')->label('Hardware Version')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Toggle::make('is_loaner')->label('Loaner Battery')
                            ->required(),
                        Forms\Components\TextInput::make('description')->label('Description')
                            ->required()
                            ->maxLength(255)->columnSpanFull(),

                        ])->columns(3),
                        //end wizard 2
                ])->columnSpanFull(),
                //end wizarad
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('model')->label('Model')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')->label('Status')
                    ->color(fn ($record) => match ($record->status){
                        'airworthy' => Color::Green,
                       'maintenance' =>Color::Red,
                       'retired' => Color::Zinc
                     })
                    ->searchable(),
                Tables\Columns\TextColumn::make('asset_inventory')->label('Inventory/Asset')
                    ->searchable(),
                Tables\Columns\TextColumn::make('serial_P')->label('Serial Printed')
                    ->sortable(),
                Tables\Columns\TextColumn::make('serial_I')->label('Serial Internal')
                    ->sortable(),
                Tables\Columns\TextColumn::make('cellCount')->label('Cell Count')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nominal_voltage')->label('Voltage')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('capacity')->label('Capacity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('initial_Cycle_count')->label('Initial Cycles Count')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('life_span')->label('Life Span')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('flaight_count')->label('Flaight Count')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('for_drone')->label('Blokec To Drone')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('purchase_date')->label('Purchase Date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('insurable_value')->label('Insurable Value')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('wight')->label('weight')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('firmware_version')->label('Firmware Version')
                    ->searchable(),
                Tables\Columns\TextColumn::make('hardware_version')->label('Hardware Version')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_loaner')->label('Is Loaner')
                    ->boolean(),
                Tables\Columns\TextColumn::make('description')->label('Description')
                    ->searchable(),
                Tables\Columns\TextColumn::make('users.name')->label('Owners')
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
            'index' => Pages\ListBattreis::route('/'),
            'create' => Pages\CreateBattrei::route('/create'),
            'view' => Pages\ViewBattrei::route('/{record}'),
            'edit' => Pages\EditBattrei::route('/{record}/edit'),
        ];
    }
}
