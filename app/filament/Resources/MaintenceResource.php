<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MaintenceResource\Pages;
use App\Filament\Resources\MaintenceResource\RelationManagers;
use App\Models\maintence_drone;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MaintenceResource extends Resource
{
    protected static ?string $model = maintence_drone::class;
    protected static ?string $navigationLabel = 'Maintenance Drone';

    protected static ?string $navigationIcon = 'heroicon-s-wrench-screwdriver';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Wizard::make([
                    Forms\Components\Wizard\Step::make('Overview')
                    ->schema([
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
                                'asset'=> 'Schedule',
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
                            ->label('Part_name')
                            ->maxLength(255),
                        Forms\Components\Select::make('status_part')
                            ->label('status_part')
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
                Tables\Columns\TextColumn::make('drone_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cost')
                    ->searchable(),
                Tables\Columns\TextColumn::make('currency')
                    ->searchable(),
                Tables\Columns\TextColumn::make('notes')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('part')
                    ->sortable(),
                Tables\Columns\TextColumn::make('part_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status_part')
                    ->sortable(),
                Tables\Columns\TextColumn::make('technician')
                    ->sortable(),
                Tables\Columns\IconColumn::make('replaced')
                    ->boolean(),
                Tables\Columns\TextColumn::make('new_part_serial')
                    ->sortable(),
                Tables\Columns\TextColumn::make('description_part')
                    ->sortable()
            ])
            ->filters([
                //
            ])
            ->actions([
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
            'index' => Pages\ListMaintences::route('/'),
            'create' => Pages\CreateMaintence::route('/create'),
            'edit' => Pages\EditMaintence::route('/{record}/edit'),
        ];
    }
}
