<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EquidmentResource\Pages;
use App\Filament\Resources\EquidmentResource\RelationManagers;
use App\Models\Equidment;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Support\Colors\Color;
use Filament\Infolists\Components\IconEntry;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\View;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\Layout\Stack;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EquidmentResource extends Resource
{
    protected static ?string $model = Equidment::class;
    protected static ?string $navigationLabel = 'Equipment' ;
    protected static ?string $navigationIcon = 'heroicon-m-cube';
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
                        Forms\Components\select::make('type')->label('Type')
                            ->options([
                                'airframe' => 'Airframe',
                                'anenometer' => 'Anenometer',
                                'battery' => 'Battery',
                                'battery_charger' => 'Battery Charger',
                                'camera' => 'Camera',
                                'charger' => 'Charger',
                                'cradle' => 'Cradle',
                                'drives(disc, flash)' => 'Drives(disc, flash)',
                                'flight_controller' => 'Flight controller',
                                'fvp_glasses' => 'FVP Glasses',
                                'gimbal' => 'Gimbal',
                                'gps' => 'GPS',
                                'lens' => 'Lens',
                                'lights' => 'Lights',
                                'monitor' => 'Monitor',
                                'motor' => 'Motor',
                                'parachute' => 'Parachute',
                                'phone/tablet' => 'Phone/Tablet',
                                'power_supply' => 'Power Supply',
                                'prop_guards' => 'Prop Guards',
                                'propeller' => 'Propeller',
                                'radio_receiver' => 'Radio Receiver',
                                'radio_transmitter' => 'Radio Transmitter',
                                'radio_extender' => 'Radio Extender',
                                'radio_finder(laser)' => 'Radio Finder(laser)',
                                'remote_controller' => 'Remote Controller',
                                'sensors' => 'Sensors',
                                'spreader' => 'Spreader',
                                'telementry_radio' => 'Telementry Radio',
                                'tripod' => 'Tripod',
                                'video_transmitter' => 'Video Transmitter'
                            ])
                            ->required()->columnSpan(2),
                        Forms\Components\Select::make('drones_id')->label('For Drone (Optional)')
                            ->relationship('drones', 'name'),
                    ])->columns(4),
                    //form ke dua
                    Forms\Components\Wizard\Step::make('Extra Information')
                        ->schema([
                        Forms\Components\Select::make('users_id')->label('Owner')
                            ->relationship('users', 'name'),
                        Forms\Components\DatePicker::make('purchase_date')->label('Purchase date')
                            ->required(),
                        Forms\Components\TextInput::make('insurable_value')->label('Insurable Value')
                            ->required()
                            ->numeric(),
                        Forms\Components\TextInput::make('weight')->label('Weight')
                            ->required()
                            ->numeric(),
                        Forms\Components\TextInput::make('firmware_v')->label('Firmware Version ')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('hardware_v')->label('Hardware Version')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Checkbox::make('is_loaner')->label('Loaner Equidment'),
                        Forms\Components\TextArea::make('description')->label('Description')
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
                     Tables\Columns\TextColumn::make('status')->label('Status')
                     ->color(fn ($record) => match ($record->status){
                         'airworthy' => Color::Green,
                        'maintenance' =>Color::Red,
                        'retired' => Color::Zinc,
                    })
                    ->searchable(),
                //  Tables\Columns\TextColumn::make('inventory_asset')
                //      ->searchable(),
                //  Tables\Columns\TextColumn::make('serial')
                //      ->numeric()
                //      ->sortable(),
                //  Tables\Columns\TextColumn::make('type')
                //      ->searchable(),
                 Tables\Columns\TextColumn::make('drones.name')
                     ->numeric()
                     ->sortable(),
                 Tables\Columns\TextColumn::make('users.name')
                    ->label('Owner')
                     ->numeric()
                     ->sortable(),
                //  Tables\Columns\TextColumn::make('purchase_date')
                //      ->date()
                //      ->sortable(),
                //  Tables\Columns\TextColumn::make('insurable_value')
                //      ->numeric()
                //      ->sortable(),
                //  Tables\Columns\TextColumn::make('weight')
                //      ->numeric()
                //      ->sortable(),
                //  Tables\Columns\TextColumn::make('firmware_v')
                //      ->searchable(),
                //  Tables\Columns\TextColumn::make('hardware_v')
                //      ->searchable(),
                //  Tables\Columns\IconColumn::make('is_loaner')
                //      ->boolean(),
                //  Tables\Columns\TextColumn::make('description')
                //      ->searchable(),
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
            TextEntry::make('name')->label('Name'),
            TextEntry::make('model')->label('Model'),
            TextEntry::make('status')->label('Status')
            ->color(fn ($record) => match ($record->status){
                'airworthy' => Color::Green,
               'maintenance' =>Color::Red,
               'retired' => Color::Zinc
             }),
            TextEntry::make('inventory_asset')->label('Inventory/Asset'),
            TextEntry::make('serial')->label('serial'),
            TextEntry::make('type')->label('type'),
            TextEntry::make('drones.name')->label('Drones'),
            TextEntry::make('users.name')->label('Owner'),
            TextEntry::make('purchase_date')->label('Purchase Date')->date('Y-m-d'),
            TextEntry::make('insurable_value')->label('Insurable Value'),
            TextEntry::make('weight')->label('Weight'),
            TextEntry::make('firmware_v')->label('Firmware Version'),
            TextEntry::make('hardware_v')->label('Hardware Version'),
            IconEntry::make('is_loaner')->boolean()->label('Loaner Equipment'),
            TextEntry::make('description')->label('Description'),
        ])->columns(3);
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
            'edit' => Pages\EditEquidment::route('/{record}/edit'),
        ];
    }
}
