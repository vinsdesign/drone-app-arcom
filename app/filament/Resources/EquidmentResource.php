<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EquidmentResource\Pages;
use App\Filament\Resources\EquidmentResource\RelationManagers;
use App\Models\drone;
use App\Models\Equidment;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Infolists\Components\Section;
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
use Carbon\Carbon;
use App\Helpers\TranslationHelper;

class EquidmentResource extends Resource
{
    protected static ?string $model = Equidment::class;
    // protected static ?string $navigationLabel = 'Equipment' ;
    protected static ?string $navigationIcon = 'heroicon-m-cube';
    // protected static?string $navigationGroup = 'Inventory';

    // protected static?string $modelLabel = 'Equipment';

    public static ?string $tenantOwnershipRelationshipName = 'teams';
    protected static bool $isLazy = false;

    public static function getNavigationBadge(): ?string{
        $teamID = Auth()->user()->teams()->first()->id;
        return static::getModel()::Where('teams_id',$teamID)->count();
    }

    public static function getNavigationLabel(): string
    {
        return TranslationHelper::translateIfNeeded('Equipment');
    }
    public static function getModelLabel(): string
    {
        return TranslationHelper::translateIfNeeded('Equipment');
    }

    public static function form(Form $form): Form
    {
        $currentTeamId = auth()->user()->teams()->first()->id;
        return $form
            ->schema([

                Tabs::make('Tabs')
                    ->tabs([
                        Tabs\Tab::make(TranslationHelper::translateIfNeeded('Overview'))
                        ->schema([
                            Forms\Components\Hidden::make('teams_id')
                                ->default(auth()->user()->teams()->first()->id ?? null),
                            Forms\Components\TextInput::make('name')
                           ->label(TranslationHelper::translateIfNeeded('Name'))
                                ->required()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('model')
                           ->label(TranslationHelper::translateIfNeeded('Model'))
                                ->required()
                                ->maxLength(255),
                            Forms\Components\select::make('status')
                           ->label(TranslationHelper::translateIfNeeded('Status'))
                                ->options([
                                    'airworthy' => 'Airworthy',
                                    'maintenance' => 'Maintenance',
                                    'retired' => 'Retired',
                                ])
                                ->required(),
                            Forms\Components\Select::make('inventory_asset')
                           ->label(TranslationHelper::translateIfNeeded('Inventory / Asset'))
                                ->options([
                                    'asset'=> 'Assets',
                                    'inventory'=> 'Inventory',
                                ])
                                ->required(),
                            Forms\Components\TextInput::make('serial')
                           ->label(TranslationHelper::translateIfNeeded('Serial'))
                                ->required()->columnSpan(2),
                            Forms\Components\select::make('type')
                           ->label(TranslationHelper::translateIfNeeded('Type'))
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
                                ])->searchable()
                                ->required()->columnSpan(2),
                            Forms\Components\Select::make('drones_id')
                           ->label(TranslationHelper::translateIfNeeded('For Drone (Optional)'))
                                // ->relationship('drones', 'name', function (Builder $query){
                                //     $currentTeamId = auth()->user()->teams()->first()->id;
                                //     $query->where('teams_id', $currentTeamId);
                                // }),
                                ->searchable()
                                ->options(function (callable $get) use ($currentTeamId) {
                                    return drone::where('teams_id', $currentTeamId)->pluck('name', 'id');
                                })
                        ])->columns(4),
                        Tabs\Tab::make(TranslationHelper::translateIfNeeded('Extra Information'))
                        ->schema([
                            Forms\Components\Select::make('users_id')
                           ->label(TranslationHelper::translateIfNeeded('Owner'))
                                //->relationship('users', 'name')
                                ->searchable()
                                ->options(function () {
                                    $currentTeamId = auth()->user()->teams()->first()->id; 
                            
                                    return User::whereHas('teams', function (Builder $query) use ($currentTeamId) {
                                        $query->where('team_user.team_id', $currentTeamId); 
                                    })->pluck('name', 'id'); 
                                }),
                            Forms\Components\DatePicker::make('purchase_date')
                           ->label(TranslationHelper::translateIfNeeded('Purchase Date'))
                                ->required(),
                            Forms\Components\TextInput::make('insurable_value')
                           ->label(TranslationHelper::translateIfNeeded('Insurable Value'))
                                ->required()
                                ->numeric(),
                            Forms\Components\TextInput::make('weight')
                           ->label(TranslationHelper::translateIfNeeded('Weight'))
                                ->required()
                                ->numeric(),
                            Forms\Components\TextInput::make('firmware_v')
                           ->label(TranslationHelper::translateIfNeeded('Firmware Version'))
                                ->required()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('hardware_v')
                           ->label(TranslationHelper::translateIfNeeded('Hardware Version'))
                                ->required()
                                ->maxLength(255),
                            Forms\Components\Toggle::make('is_loaner')
                           ->label(TranslationHelper::translateIfNeeded('Loaner Equipment'))
                           ->onColor('success')
                            ->offColor('danger'),
                            Forms\Components\TextArea::make('description')
                           ->label(TranslationHelper::translateIfNeeded('Description'))
                                ->maxLength(255),
                            ])->columns(3),
                    ])->columnSpanFull(),

            ]);
    }




    public static function table(Table $table): Table
    {
        return $table
            //edit query untuk action shared un-shared
            ->modifyQueryUsing(function (Builder $query) {
                $userId = auth()->user()->id;

                if (Auth()->user()->roles()->pluck('name')->contains('super_admin') || (Auth()->user()->roles()->pluck('name')->contains('panel_user'))) {
                    return $query;
                }else{
                    $query->where(function ($query) use ($userId) {
                        $query->where('users_id', $userId);
                    })
                    ->orWhere(function ($query) use ($userId) {
                        $query->where('users_id', '!=', $userId)->where('shared', 1);
                    });
                    return $query;
                }
            })
            ->columns([
                Tables\Columns\TextColumn::make('name')
               ->label(TranslationHelper::translateIfNeeded('Name'))
                     ->searchable(),
                Tables\Columns\TextColumn::make('model')
               ->label(TranslationHelper::translateIfNeeded('Model'))
                     ->searchable(),
                Tables\Columns\TextColumn::make('type')
               ->label(TranslationHelper::translateIfNeeded('Type'))
                     ->searchable(),
                Tables\Columns\TextColumn::make('flight_time')
                   ->label(TranslationHelper::translateIfNeeded('Flights & Flying Time'))
                     ->getStateUsing(function ($record) {
                        $flights = $record->fligh()
                            ->whereHas('teams', function ($query) {
                                $query->where('teams.id', auth()->user()->teams()->first()->id);
                            })
                            ->get()
                            ->merge(
                                $record->kits()->with(['fligh' => function ($query) {
                                    $query->whereHas('teams', function ($query) {
                                        $query->where('teams.id', auth()->user()->teams()->first()->id);
                                    });
                                }])->get()->pluck('fligh')->flatten()
                            );
                    
                        $totalSeconds = 0;
                        foreach ($flights as $flight) {
                            $start = $flight->start_date_flight;
                            $end = $flight->end_date_flight;
                            if ($start && $end) {
                                $totalSeconds += Carbon::parse($start)->diffInSeconds(Carbon::parse($end));
                            }
                        }
                    
                        $hours = floor($totalSeconds / 3600);
                        $minutes = floor(($totalSeconds % 3600) / 60);
                        $seconds = $totalSeconds % 60;
                        $totalDuration = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
                    
                        $totalFlights = $flights->unique('id')->count();
                        return "<div> ({$totalFlights}) ". TranslationHelper::translateIfNeeded('Flights') ." <div class='inline-block border border-gray-300 dark:border-gray-600 px-2 py-1 rounded bg-gray-200 dark:bg-gray-700'>
                            <strong class='text-gray-800 dark:text-gray-200'>{$totalDuration}</strong></div>";
                    })
                     ->sortable()
                     ->html(),
                Tables\Columns\TextColumn::make('status')
               ->label(TranslationHelper::translateIfNeeded('Status'))
                    //  ->color(fn ($record) => match ($record->status){
                    //      'airworthy' => Color::Green,
                    //     'maintenance' =>Color::Red,
                    //     'retired' => Color::Zinc,
                    // })
                    ->formatStateUsing(function ($state) {
                        $colors = [
                            'airworthy' => '#28a745',
                            'maintenance' => 'red',
                            'retired' => 'gray',
                        ];
                
                        $color = $colors[$state] ?? 'gray';
                
                        return "<span style='
                                display: inline-block;
                                width: 10px;
                                height: 10px;
                                background-color: {$color};
                                border-radius: 50%;
                                margin-right: 5px;
                            '></span><span style='color: {$color};'>{$state}</span>";
                    })
                    ->html()
                    ->searchable()
                    ->badge(),
                 Tables\Columns\TextColumn::make('drones.name')
                ->label(TranslationHelper::translateIfNeeded('For Drone'))
                     ->numeric()
                     ->url(fn($record) =>$record->for_drone? route('filament.admin.resources.drones.view', [
                        'tenant' => Auth()->user()->teams()->first()->id,
                        'record' => $record->for_drone,
                    ]):null)->color(Color::Blue)
                     ->sortable()
                     ->placeholder(TranslationHelper::translateIfNeeded('No drone selected')),
                 Tables\Columns\TextColumn::make('users.name')
                 ->label(TranslationHelper::translateIfNeeded('Users'))
                    ->url(fn($record) => $record->users_id? route('filament.admin.resources.users.view', [
                        'tenant' => Auth()->user()->teams()->first()->id,
                        'record' => $record->users_id,
                    ]):null)->color(Color::Blue)
                     ->numeric()
                     ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                ->options([
                    'airworthy' => 'Airworthy',
                   'maintenance' => 'Maintenance',
                   'retired' => 'Retired'
                ])
                ->label(TranslationHelper::translateIfNeeded('Filter by Status')),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('showEquipment')
                    ->url(fn ($record) => route('equipment.statistik', ['equipment_id' => $record->id]))->label(TranslationHelper::translateIfNeeded('View'))
                    ->icon('heroicon-s-eye'),

                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    //Shared action
                    Tables\Actions\Action::make('Shared')->label(TranslationHelper::translateIfNeeded('Shared'))
                    ->hidden(fn ($record) => 
                    ($record->shared == 1) ||
                    !(Auth()->user()->roles()->pluck('name')->contains('super_admin') || (Auth()->user()->roles()->pluck('name')->contains('panel_user'))) && 
                    ($record->users_id != Auth()->user()->id))
    
                    ->action(function ($record) {
                        $record->update(['shared' => 1]);
                        Notification::make()
                        ->title(TranslationHelper::translateIfNeeded('Shared Updated'))
                        ->body(TranslationHelper::translateIfNeeded("Shared successfully changed."))
                        ->success()
                        ->send();
                    })->icon('heroicon-m-share'),
                //Un-Shared action
                Tables\Actions\Action::make('Un-Shared')->label(TranslationHelper::translateIfNeeded('Un-Shared'))
                    ->hidden(fn ($record) => 
                    ($record->shared == 0) ||
                    !(Auth()->user()->roles()->pluck('name')->contains('super_admin') || (Auth()->user()->roles()->pluck('name')->contains('panel_user')))&&
                    ($record->users_id != Auth()->user()->id))
                    ->action(function ($record) {
                        $record->update(['shared' => 0]);
                        Notification::make()
                        ->title(TranslationHelper::translateIfNeeded('Un-Shared Updated '))
                        ->body(TranslationHelper::translateIfNeeded("Un-Shared successfully changed."))
                        ->success()
                        ->send();
                    })->icon('heroicon-m-share'),
                ])
                
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
            Section::make(TranslationHelper::translateIfNeeded('Overview'))
                ->schema([
                    TextEntry::make('name')->label(TranslationHelper::translateIfNeeded('Name')),
                    TextEntry::make('model')->label(TranslationHelper::translateIfNeeded('Model')),
                    TextEntry::make('status')->label(TranslationHelper::translateIfNeeded('Status'))
                        ->color(fn ($record) => match ($record->status) {
                            'airworthy' => Color::Green,
                            'maintenance' => Color::Red,
                            'retired' => Color::Zinc
                        }),
                    TextEntry::make('inventory_asset')->label(TranslationHelper::translateIfNeeded('Inventory/Asset')),
                    TextEntry::make('serial')->label(TranslationHelper::translateIfNeeded('Serial')),
                    TextEntry::make('type')->label(TranslationHelper::translateIfNeeded('Type')),
                    TextEntry::make('drones.name')->label(TranslationHelper::translateIfNeeded('Drones'))
                        ->url(fn($record) => $record->for_drone ? route('filament.admin.resources.drones.view', [
                            'tenant' => Auth()->user()->teams()->first()->id,
                            'record' => $record->for_drone,
                        ]) : null)->color(Color::Blue),
                ])->columns(4),
            Section::make(TranslationHelper::translateIfNeeded('Extra Information'))
                ->schema([
                    TextEntry::make('users.name')->label(TranslationHelper::translateIfNeeded('Owner'))
                        ->url(fn($record) => $record->users_id ? route('filament.admin.resources.users.view', [
                            'tenant' => Auth()->user()->teams()->first()->id,
                            'record' => $record->users_id,
                        ]) : null)->color(Color::Blue),
                    TextEntry::make('purchase_date')->label(TranslationHelper::translateIfNeeded('Purchase Date'))->date('Y-m-d'),
                    TextEntry::make('insurable_value')->label(TranslationHelper::translateIfNeeded('Insurable Value')),
                    TextEntry::make('weight')->label(TranslationHelper::translateIfNeeded('Weight')),
                    TextEntry::make('firmware_v')->label(TranslationHelper::translateIfNeeded('Firmware Version')),
                    TextEntry::make('hardware_v')->label(TranslationHelper::translateIfNeeded('Hardware Version')),
                    IconEntry::make('is_loaner')->boolean()->label(TranslationHelper::translateIfNeeded('Loaner Equipment')),
                    TextEntry::make('description')->label(TranslationHelper::translateIfNeeded('Description')),
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
            'index' => Pages\ListEquidments::route('/'),
            'create' => Pages\CreateEquidment::route('/create'),
            'edit' => Pages\EditEquidment::route('/{record}/edit'),
            'view' => Pages\ViewEquidment::route('/{record}')
        ];
    }
}
