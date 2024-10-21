<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MaintenanceBatteryResource\Pages;
use App\Filament\Resources\MaintenanceBatteryResource\RelationManagers;
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

class MaintenanceBatteryResource extends Resource
{
    protected static ?string $model = maintence_eq::class;

    protected static ?string $navigationLabel = 'Maintenance Equipment';
    protected static ?string $tenantRelationshipName = 'maintence_eqs';
    protected static ?string $modelLabel = 'Maintenance Equipment';

    protected static ?string $navigationIcon = 'heroicon-o-cog';
    public static ?string $tenantOwnershipRelationshipName = 'teams';
    public static ?int $navigationSort = 6;
    public static ?string $navigationGroup = 'Maintenance';

    

    public static function form(Form $form): Form
    {
        $currentTeamId = auth()->user()->teams()->first()->id;
        return $form
            ->schema([
                Forms\Components\Section::make('Maintenance Battery Overview')
                    ->schema([
                        Forms\Components\Hidden::make('teams_id')
                        ->default(auth()->user()->teams()->first()->id ?? null),
                        Forms\Components\TextInput::make('name')
                            ->label('Maintenance Description')
                            ->maxLength(255),
                        Forms\Components\Select::make('equidment_id')
                            ->label('Equipment')
                            // ->relationship('equidment','name', function (Builder $query){
                            //     $currentTeamId = auth()->user()->teams()->first()->id;
                            //     $query->where('teams_id', $currentTeamId);
                            // })
                            ->options(function (callable $get) use ($currentTeamId) {
                                return equidment::where('teams_id', $currentTeamId)->pluck('name', 'id');
                            })
                            ->searchable()
                            ->columnSpan(1),
                        Forms\Components\DatePicker::make('date')
                            ->label('Maintenance Date')   
                            ->columnSpan(1),
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'schedule'=> 'Schedule',
                                'in_progress'=> 'In Progress',
                                'completed'=> 'Completed',
                            ]),
                        Forms\Components\TextInput::make('cost')
                            ->label('Expense Cost'),
                        Forms\Components\TextInput::make('currency')
                            ->label('Currency'),
                        Forms\Components\TextArea::make('notes')
                            ->label('Notes')
                            ->columnSpanFull(),
                ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                ->searchable(),
                Tables\Columns\TextColumn::make('equidment.name')
                ->label('Equipment')
                ->url(fn($record) => $record->equidment_id?route('filament.admin.resources.equidments.index', [
                    'tenant' => Auth()->user()->teams()->first()->id,
                    'record' => $record->equidment_id,
                ]):null)->color(Color::Blue)
                ->searchable(),
                Tables\Columns\TextColumn::make('date')
                ->date()
                ->searchable()
                ->formatStateUsing(function ($state, $record) {
                    $daysOverdue = Carbon::parse($state);
                    $now = Carbon::now();
                    $formatDate = $daysOverdue->format('Y-m-d');

                    if ($record->status !== 'completed') {
                        $daysOverdue = $now->diffInDays($daysOverdue, false);

                        if ($daysOverdue < 0){
                            $daysOverdue = abs(intval($daysOverdue));
                            return "<div>{$formatDate}<br><span style='
                            display: inline-block;
                            background-color: red; 
                            color: white; 
                            padding: 3px 6px;
                            border-radius: 5px;
                            font-weight: bold;
                        '>
                            Overdue: {$daysOverdue} days
                        </span>
                    </div>";
                        }
                    }
                    return $daysOverdue->format('Y-m-d');
                })
                ->html(),
                Tables\Columns\TextColumn::make('status')
                ->color(fn ($record) => match ($record->status){
                    'completed' => Color::Green,
                   'schedule' =>Color::Red,
                   'in_progress' => Color::Zinc
                 })
                ->searchable(),
                Tables\Columns\TextColumn::make('cost')
                ->searchable(),
                Tables\Columns\TextColumn::make('currency')
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
                    ->action(function ($record){
                        $record->status = 'completed';
                        $record->save();
                        Notification::make()
                            ->title('Task Resolved')
                            ->body('The task has been successfully resolved.')
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->visible(function ($record){
                        return $record->status !== 'completed';
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
            TextEntry::make('name')->label('Name'),
            TextEntry::make('equidment.name')->label('Equipment')
                ->url(fn($record) => $record->equidment_id?route('filament.admin.resources.equidments.index', [
                    'tenant' => Auth()->user()->teams()->first()->id,
                    'record' => $record->equidment_id,
                ]):null)->color(Color::Blue),
            TextEntry::make('date')->label('Date'),
            TextEntry::make('status')->label('Status'),
            TextEntry::make('cost')->label('Cost'),
            TextEntry::make('currency')->label('Currency'),
            TextEntry::make('notes')->label('Notes'), 
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
