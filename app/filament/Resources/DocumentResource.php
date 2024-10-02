<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DocumentResource\Pages;
use App\Filament\Resources\DocumentResource\RelationManagers;
use App\Models\Document;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DocumentResource extends Resource
{
    protected static ?string $model = Document::class;

    protected static ?string $navigationIcon = 'heroicon-m-document-text';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('')
                ->description('')
                ->schema([
                    Forms\Components\TextInput::make('name')->label('Title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('type')->label('Type')
                    ->required()
                    ->options([
                        'Pilot License' => 'Pilot License',
                        'UAV Training Course' => 'UAV Training Course',
                        'Remote Pilot Certificate' => 'Remote Pilot Certificate',
                        'Currency Certificate' => 'Currency Certificate',
                        'Medical Certificate' => 'Medical Certificate',
                        'Insurance Certificate' => 'Insurance  Certificate',
                        'Registration #' => 'Registration #',
                        'Regulatory Certificate' => 'Regulatory Certificate',
                        'Checklist' => 'Checklist',
                        'Manual' => 'Manual',
                        'Other Certificate' => 'Other Certificate',
                        'Site Assessment' => 'Site Assessment',
                        'Safety Instruction' => 'Safety Instruction',
                        'Other' => 'Other',
                    ])->searchable()->columnSpan(2),
                Forms\Components\TextInput::make('refnumber')->label('REF.Number')
                    ->required()
                    ->maxLength(255),
                Forms\Components\DatePicker::make('expired_date')->label('Expired Date')
                    ->required(),
                Forms\Components\Select::make('customers_id')->label('Customer')
                    ->relationship('customers', 'name'),
                Forms\Components\Select::make('scope')->label('Scope')
                    ->required()
                    ->options([
                        'Flight' => 'Flight',
                        'Organization' => 'Organization',
                        'Pilot' => 'Pilot',
                        'Project' => 'Project',
                        'Drones' => 'Drones',
                        'Equidments/Battry' => 'Equidments/Battry',
                        'Incident' => 'Incident',
                    ])
                    ->columnSpan(2),
                Forms\Components\Select::make('users_id')->label('Owner')
                    ->relationship('users', 'name')
                    ->required(),
                    Forms\Components\Select::make('projects_id')->label('Project / Job Reference')
                    ->relationship('projects', 'case')
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('doc')->label('Upload Document'),
                Forms\Components\TextInput::make('external link')->label('Or External Link,your document')
                    ->required()
                    ->maxLength(255)->columnSpan(2),
                Forms\Components\TextArea::make('description')->label('Description')
                    ->maxLength(255)->columnSpanFull(),

                ])->columns(3),
                
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('refnumber')->label('REG Number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('users.name')->label('Owner')
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')->label('Type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('expired_date')->label('Expired Date')
                    ->date('Y-m-d')
                    ->sortable(),
                Tables\Columns\TextColumn::make('scope')->label('Scope')
                    ->searchable(),
                Tables\Columns\TextColumn::make('external link')->label('External Link')
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')->label('Description')
                    ->searchable(),
                    //Belum bisa Link ke Document
                Tables\Columns\TextColumn::make('doc')->label('Document')
                    ->searchable(),
                Tables\Columns\TextColumn::make('customers_id')->label('Customer')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('projects_id')->label('Project')
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
            'index' => Pages\ListDocuments::route('/'),
            'create' => Pages\CreateDocument::route('/create'),
            'view' => Pages\ViewDocument::route('/{record}'),
            'edit' => Pages\EditDocument::route('/{record}/edit'),
        ];
    }
}
