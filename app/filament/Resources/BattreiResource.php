<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BattreiResource\Pages;
use App\Filament\Resources\BattreiResource\RelationManagers;
use App\Models\Battrei;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\IconEntry;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;


class BattreiResource extends Resource
{
    protected static ?string $model = Battrei::class;
    protected static ?string $navigationGroup = 'Inventory';
    protected static ?string $navigationLabel = 'Batteries';
    protected static ?string $modelLabel = 'Batteries';
    protected static ?string $navigationIcon = 'heroicon-s-battery-100';
    public static ?string $tenantOwnershipRelationshipName = 'teams';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Wizard::make([
                    Forms\Components\Wizard\Step::make('Overview')
                        ->schema([
                        Forms\Components\Hidden::make('teams_id')
                            ->default(auth()->user()->teams()->first()->id ?? null),
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
                            ->relationship('drone', 'name', function (Builder $query){
                                $currentTeamId = auth()->user()->teams()->first()->id;
                                $query->where('teams_id', $currentTeamId);
                            })
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
                            //->relationship('users', 'name')
                            ->options(function () {
                                $currentTeamId = auth()->user()->teams()->first()->id; 
                        
                                return User::whereHas('teams', function (Builder $query) use ($currentTeamId) {
                                    $query->where('team_user.team_id', $currentTeamId); 
                                })->pluck('name', 'id'); 
                            }) 
                            ->required(),
                        Forms\Components\DatePicker::make('purchase_date')->label('Purchase date')
                            ->required(),
                        Forms\Components\TextInput::make('insurable_value')->label('Insurable Value')
                            ->required()
                            ->numeric(),
                        Forms\Components\TextInput::make('wight')->label('Weight')
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
                // Tables\Columns\TextColumn::make('asset_inventory')->label('Inventory/Asset')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('serial_P')->label('Serial Printed')
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('serial_I')->label('Serial Internal')
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('cellCount')->label('Cell Count')
                //     ->numeric()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('nominal_voltage')->label('Voltage')
                //     ->numeric()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('capacity')->label('Capacity')
                //     ->numeric()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('initial_Cycle_count')->label('Initial Cycles Count')
                //     ->numeric()
                //     ->sortable(),
                Tables\Columns\TextColumn::make('life_span')->label('Life Span')
                    ->numeric()
                    ->sortable(),
                // Tables\Columns\TextColumn::make('flaight_count')->label('Flaight Count')
                //     ->numeric()
                //     ->sortable(),
                Tables\Columns\TextColumn::make('drone.name')->label('Blokec To Drone')
                    ->numeric()->url(fn($record) => $record->for_drone ? route('filament.admin.resources.drones.index', [
                        'tenant' => Auth()->user()->teams()->first()->id,
                        'record' => $record->for_drone,
                    ]): null)->color(Color::Blue)
                    ->sortable(),
                // Tables\Columns\TextColumn::make('purchase_date')->label('Purchase Date')
                //     ->date()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('insurable_value')->label('Insurable Value')
                //     ->numeric()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('wight')->label('weight')
                //     ->numeric()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('firmware_version')->label('Firmware Version')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('hardware_version')->label('Hardware Version')
                //     ->searchable(),
                // Tables\Columns\IconColumn::make('is_loaner')->label('Is Loaner')
                //     ->boolean(),
                // Tables\Columns\TextColumn::make('description')->label('Description')
                //     ->searchable(),
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
                Tables\Filters\SelectFilter::make('status')
                ->options([
                    'airworthy' => 'Airworthy',
                   'maintenance' => 'Maintenance',
                   'retired' => 'Retired'
                ])
                ->label('Filter by Status'),
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
//infolist battery
public static function infolist(Infolist $infolist): Infolist
{
    return $infolist
    
    ->schema([
        Section::make('Overview')
                ->schema([
        TextEntry::make('name')->label('Name'),
        TextEntry::make('model')->label('Model'),
        TextEntry::make('status')->label('Status')
        ->color(fn ($record) => match ($record->status){
            'airworthy' => Color::Green,
           'maintenance' =>Color::Red,
           'retired' => Color::Zinc
         }),
        TextEntry::make('asset_inventory')->label('Asset Inventory'),
        TextEntry::make('serial_P')->label('Serial Printed'),
        TextEntry::make('serial_I')->label('Serial Internal'),
        TextEntry::make('cellCount')->label('Cell Count'),
        TextEntry::make('nominal_voltage')->label('Voltage'),
        TextEntry::make('capacity')->label('Capacity'),
        TextEntry::make('initial_Cycle_count')->label('Initial Cycles Count'),
        TextEntry::make('life_span')->label('Life Span'),
        TextEntry::make('flaight_count')->label('Flaight Count'),
        TextEntry::make('drone.name')->label('For Drone (Optional)')
        ->url(fn($record) => $record->for_drone ? route('filament.admin.resources.drones.index', [
            'tenant' => Auth()->user()->teams()->first()->id,
            'record' => $record->for_drone,
        ]): null)->color(Color::Blue),
                ])->columns(5),
            Section::make('Extra Information')
                ->schema([
                    TextEntry::make('users.name')->label('Owner')
                    ->url(fn($record) => $record->for_drone ? route('filament.admin.resources.users.index', [
                        'tenant' => Auth()->user()->teams()->first()->id,
                        'record' => $record->users_id,
                    ]): null)->color(Color::Blue),
                    TextEntry::make('purchase_date')->label('Purchase date'),
                TextEntry::make('insurable_value')->label('Insurable Value'),
                TextEntry::make('wight')->label('Weight'),
                TextEntry::make('firmware_version')->label('Firmware Version'),
                TextEntry::make('hardware_version')->label('Hardware Version'),
                IconEntry::make('is_loaner')->boolean()->label('Loaner Battery'),
                TextEntry::make('description')->label('Description'),
    ])->columns(4)
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
            //'view' => Pages\ViewBattrei::route('/{record}'),
            'edit' => Pages\EditBattrei::route('/{record}/edit'),
        ];
    }
}
