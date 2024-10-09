<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MaintenceResource\Pages;
use App\Filament\Resources\MaintenceResource\RelationManagers;
use App\Models\Maintence_drone;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\IconEntry;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MaintenceResource extends Resource
{
    protected static ?string $model = Maintence_drone::class;
    protected static ?string $navigationLabel = 'Maintenance Drone';
    protected static ?string $tenantRelationshipName = 'maintence_drones';
    protected static ?string $modelLabel = 'Maintenance Drone';

    protected static ?string $navigationIcon = 'heroicon-s-wrench-screwdriver';
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
                        Forms\Components\TextInput::make('name')
                            ->label('Maintenance Description')
                            ->maxLength(255),
                        Forms\Components\Select::make('drone_id')
                            ->relationship('drone','name')
                            ->columnSpan(1),
                        Forms\Components\DatePicker::make('date')
                            ->label('Maintenance Date')   
                            ->columnSpan(1),
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'Schedule'=> 'Schedule',
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
                    //and wizard 1
                    Forms\Components\Wizard\Step::make('Add Tasks (Optional)')
                    ->schema([
                        Forms\Components\Select::make('part')
                            ->label('Part #')
                            ->options([
                                'part 1'=> 'Part 1',
                                'part 2'=> 'Part 2',
                                'part 3'=> 'Part 3',
                            ]),
                        Forms\Components\TextInput::make('part_name')
                            ->label('Part Name')
                            ->maxLength(255),
                        Forms\Components\Select::make('status_part')
                            ->label('Status Part')
                            ->options([
                                'partial'=> 'Partial',
                                'open'=> 'Open',
                                'done'=> 'Done',
                            ]),
                        Forms\Components\TextInput::make('technician')
                            ->label('Technician')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('new_part_serial')
                            ->label('New Part Serial #')
                            ->maxLength(255),
                        Forms\Components\Checkbox::make('replaced')->label('Replaced'),
                        Forms\Components\Textarea::make('description_part')
                        ->label('Description')
                            ->maxLength(255)->columnSpanFull(),
                    ])->columns(2),
                    //and wizard 2
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
                Tables\Columns\TextColumn::make('drone.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->searchable(),
                Tables\Columns\TextColumn::make('cost')
                    ->searchable(),
                Tables\Columns\TextColumn::make('currency')
                    ->searchable(),
                Tables\Columns\TextColumn::make('technician')
                    ->sortable(),
                Tables\Columns\IconColumn::make('replaced')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
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
            Section::make('Overview')
                ->schema([
                    TextEntry::make('name')->label('Name'),
                    TextEntry::make('drone.name')->label('Drone'),
                    TextEntry::make('date')->label('Date'),
                    TextEntry::make('status')->label('Status'),
                    TextEntry::make('cost')->label('Cost'),
                    TextEntry::make('currency')->label('Currency'),
                    TextEntry::make('notes')->label('Notes')
                ])->columns(4),
            Section::make('Add Tasks (Optional)')
                ->schema([
                    TextEntry::make('part')->label('Part'),
                    TextEntry::make('part_name')->label('Part Name'),
                    TextEntry::make('status_part')->label('Status Part'),
                    TextEntry::make('technician')->label('Technician'),
                    IconEntry::make('replaced')->boolean()->label('Replaced'),
                    TextEntry::make('new_part_serial')->label('New Part Serial'),
                    TextEntry::make('description_part')->label('Description Part')
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
            'index' => Pages\ListMaintences::route('/'),
            'create' => Pages\CreateMaintence::route('/create'),
            'edit' => Pages\EditMaintence::route('/{record}/edit'),
        ];
    }
}
