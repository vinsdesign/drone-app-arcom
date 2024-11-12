<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MaintenanceBatteryResource\Pages;
use App\Filament\Resources\MaintenanceBatteryResource\RelationManagers;
use App\Models\currencie;
use App\Models\battrei;
use App\Models\equidment;
use App\Models\maintence_eq;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Support\Colors\Color;
use Stichoza\GoogleTranslate\GoogleTranslate;

class MaintenanceBatteryResource extends Resource
{
    protected static ?string $model = maintence_eq::class;

    // protected static ?string $navigationLabel = 'Maintenance Equipment/Battery';
    protected static ?string $tenantRelationshipName = 'maintence_eqs';
    // protected static ?string $modelLabel = 'Maintenance Equipment/Battery';

    protected static ?string $navigationIcon = 'heroicon-o-cog';
    public static ?string $tenantOwnershipRelationshipName = 'teams';
    public static ?int $navigationSort = 6;
    public static ?string $navigationGroup = 'Maintenance';
    protected static bool $isLazy = false;

    public static function getNavigationBadge(): ?string{
        return static::getModel()::where('status','!=','completed')->count();
    }

    public static function getNavigationLabel(): string
    {
        return GoogleTranslate::trans('Maintenance Equipment/Battery', session('locale') ?? 'en');
    }
    public static function getModelLabel(): string
    {
        return GoogleTranslate::trans('Maintenance Equipment/Battery', session('locale') ?? 'en');
    }

    public static function form(Form $form): Form
    {
        $currentTeamId = auth()->user()->teams()->first()->id;
        return $form
            ->schema([
                Forms\Components\Section::make('Maintenance Equipment/Battery Overview')
                    ->schema([
                        Forms\Components\Hidden::make('teams_id')
                        ->default(auth()->user()->teams()->first()->id ?? null),
                        Forms\Components\TextInput::make('name')
                            ->label(GoogleTranslate::trans('Maintenance Description', session('locale') ?? 'en'))
                            ->maxLength(255),
                        Forms\Components\Select::make('equidment_id')
                            ->label(GoogleTranslate::trans('Equipment', session('locale') ?? 'en'))
                            ->options(function (callable $get) use ($currentTeamId) {
                                return equidment::where('teams_id', $currentTeamId)->pluck('name', 'id');
                            })
                            ->searchable()
                            ->columnSpan(1),
                            Forms\Components\Select::make('battrei_id')
                            ->label(GoogleTranslate::trans('Battery', session('locale') ?? 'en'))
                            ->options(function (callable $get) use ($currentTeamId) {
                                return battrei::where('teams_id', $currentTeamId)->pluck('name', 'id');
                            })
                            ->searchable()
                            ->columnSpan(1),
                        Forms\Components\DatePicker::make('date')
                            ->label(GoogleTranslate::trans('Maintenance Date', session('locale') ?? 'en'))   
                            ->columnSpan(1),
                        Forms\Components\Select::make('status')
                            ->label(GoogleTranslate::trans('Status', session('locale') ?? 'en'))
                            ->options([
                                'schedule'=> 'Schedule',
                                'in_progress'=> 'In Progress',
                                'completed'=> 'Completed',
                            ]),
                        Forms\Components\TextInput::make('cost')
                            ->label(GoogleTranslate::trans('Expense Cost', session('locale') ?? 'en')),
                        Forms\Components\Select::make('currencies_id')
                        ->options(currencie::all()->mapWithKeys(function ($currency) {
                            return [$currency->id => "{$currency->name} - {$currency->iso}"];}))
                            ->searchable()
                            ->label(GoogleTranslate::trans('Currency', session('locale') ?? 'en'))
                            ->default(function (){
                                $currentTeam = auth()->user()->teams()->first();
                                return $currentTeam ? $currentTeam->currencies_id : null;
                            }),
                        Forms\Components\TextArea::make('notes')
                            ->label(GoogleTranslate::trans('Notes', session('locale') ?? 'en'))
                            ->columnSpanFull(),
                ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                ->label(GoogleTranslate::trans('Name', session('locale') ?? 'en'))
                ->searchable(),
                Tables\Columns\TextColumn::make('equidment.name')
                ->label(GoogleTranslate::trans('Equipment', session('locale') ?? 'en'))
                ->url(fn($record) => $record->equidment_id?route('filament.admin.resources.equidments.index', [
                    'tenant' => Auth()->user()->teams()->first()->id,
                    'record' => $record->equidment_id,
                ]):null)->color(Color::Blue)
                ->searchable(),
                Tables\Columns\TextColumn::make('battrei.name')
                ->label(GoogleTranslate::trans('Battery', session('locale') ?? 'en'))
                ->url(fn($record) => $record->battrei_id?route('filament.admin.resources.battreis.index', [
                    'tenant' => Auth()->user()->teams()->first()->id,
                    'record' => $record->battrei_id,
                ]):null)->color(Color::Blue)
                ->searchable(),
                Tables\Columns\TextColumn::make('date')
                ->label(GoogleTranslate::trans('Date', session('locale') ?? 'en'))
                ->date()
                ->searchable()
                ->formatStateUsing(function ($state, $record) {
                    $daysOverdue = Carbon::parse($state);
                    $now = Carbon::now();
                    $formatDate = $daysOverdue->format('Y-m-d');

                    if ($record->status !== 'completed') {
                        $daysOverdueDiff = $now->diffInDays($daysOverdue, false);

                        if ($daysOverdueDiff < 0){
                            $daysOverdueDiff = abs(intval($daysOverdueDiff));
                            return "<div>{$formatDate}<br><span style='
                            display: inline-block;
                            background-color: red; 
                            color: white; 
                            padding: 3px 6px;
                            border-radius: 5px;
                            font-weight: bold;
                        '>
                            Overdue: {$daysOverdueDiff} days
                        </span>
                    </div>";
                        }
                    }
                    // return $daysOverdue->format('Y-m-d');
                    return $formatDate;
                })
                ->html(),
                Tables\Columns\TextColumn::make('status')
                ->label(GoogleTranslate::trans('Status', session('locale') ?? 'en'))
                ->color(fn ($record) => match ($record->status){
                    'completed' => Color::Green,
                   'schedule' =>Color::Red,
                   'in_progress' => Color::Blue
                 })
                ->searchable(),
                Tables\Columns\TextColumn::make('cost')
                ->label(GoogleTranslate::trans('Cost', session('locale') ?? 'en'))
                ->searchable(),
                Tables\Columns\TextColumn::make('currencies.iso')
                ->label(GoogleTranslate::trans('Currencies', session('locale') ?? 'en'))
                ->searchable(),
                // Tables\Columns\TextColumn::make('notes')
                // ->searchable(), 
            ])
        
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                ->options([
                    'Schedule' => 'Schedule',
                    'in_progress' => 'In Progress',
                    'completed' => 'Completed'
                ])
                ->label('Filter by Status'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('resolve')
                    ->label('Resolve')
                    ->icon('heroicon-o-check-circle')
                    ->action(function ($record){
                        $record->status = 'completed';
                        $record->save();
                        Notification::make()
                            ->title('Task Resolved')
                            ->body('The task has been successfully resolved.')
                            ->send();
                    })
                    ->button()
                    ->requiresConfirmation()
                    ->visible(function ($record){
                        return $record->status !== 'completed' && auth()->user()->hasRole(['maintenance', 'panel_user']);
                    })
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
            TextEntry::make('name')->label(GoogleTranslate::trans('Name', session('locale') ?? 'en')),
            TextEntry::make('equidment.name')->label(GoogleTranslate::trans('Equipment', session('locale') ?? 'en'))
                ->url(fn($record) => $record->equidment_id?route('filament.admin.resources.equidments.index', [
                    'tenant' => Auth()->user()->teams()->first()->id,
                    'record' => $record->equidment_id,
                ]):null)->color(Color::Blue),
            TextEntry::make('battrei.name')->label(GoogleTranslate::trans('Battery', session('locale') ?? 'en'))
                ->url(fn($record) => $record->battrei_id?route('filament.admin.resources.battreis.index', [
                    'tenant' => Auth()->user()->teams()->first()->id,
                    'record' => $record->battrei_id,
                ]):null)->color(Color::Blue),
            TextEntry::make('date')->label(GoogleTranslate::trans('Date', session('locale') ?? 'en')),
            TextEntry::make('status')->label(GoogleTranslate::trans('Status', session('locale') ?? 'en')),
            TextEntry::make('cost')->label(GoogleTranslate::trans('Cost', session('locale') ?? 'en')),
            TextEntry::make('currencies.iso')->label(GoogleTranslate::trans('Currency', session('locale') ?? 'en')),
            TextEntry::make('notes')->label(GoogleTranslate::trans('Notes', session('locale') ?? 'en')), 
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
            'index' => Pages\ListMaintenanceBatteries::route('/'),
            'create' => Pages\CreateMaintenanceBattery::route('/create'),
            'edit' => Pages\EditMaintenanceBattery::route('/{record}/edit'),
        ];
    }
}
