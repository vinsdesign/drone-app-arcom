<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DocumentResource\Pages;
use App\Filament\Resources\DocumentResource\RelationManagers;
use App\Models\customer;
use App\Models\Document;
use App\Models\Projects;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Carbon\Carbon;
use Filament\Support\Colors\Color;

class DocumentResource extends Resource
{
    protected static ?string $model = Document::class;

    protected static ?string $navigationIcon = 'heroicon-m-document-text';
    public static ?string $tenantOwnershipRelationshipName = 'teams';

    public static ?int $navigationSort = 3;
    public static ?string $navigationGroup = ' ';
    protected static bool $isLazy = false;
    

    public static function getNavigationLabel(): string
    {
        return 'Documents'; // Sesuaikan dengan label yang diinginkan
    }

    public static function form(Form $form): Form
    {
        $currentTeamId = auth()->user()->teams()->first()->id;
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
                    // ->relationship('customers', 'name', function (Builder $query){
                    //     $currentTeamId = auth()->user()->teams()->first()->id;
                    //     $query->where('teams_id', $currentTeamId);
                    // }),
                    ->options(function (callable $get) use ($currentTeamId) {
                        return customer::where('teams_id', $currentTeamId)->pluck('name', 'id');
                    })
                    ->searchable(),
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
                    //->relationship('users', 'name')
                    ->options(function () {
                        $currentTeamId = auth()->user()->teams()->first()->id; 
                
                        return User::whereHas('teams', function (Builder $query) use ($currentTeamId) {
                            $query->where('team_user.team_id', $currentTeamId); 
                        })->pluck('name', 'id'); 
                    })
                    ->searchable()
                    ->required(),
                    Forms\Components\Select::make('projects_id')->label('Project / Job Reference')
                    // ->relationship('projects', 'case', function (Builder $query){
                    //     $currentTeamId = auth()->user()->teams()->first()->id;;
                    //     $query->where('teams_id', $currentTeamId);
                    // })
                    ->searchable()
                    ->options(function (callable $get) use ($currentTeamId) {
                        return projects::where('teams_id', $currentTeamId)->pluck('case', 'id');
                    })
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('doc')->label('Upload Document')
                    ->acceptedFileTypes(['application/pdf']),
                Forms\Components\TextInput::make('external link')->label('Or External Link,your document')
                    ->required()
                    ->maxLength(255)->columnSpan(2),
                Forms\Components\TextArea::make('description')->label('Description')
                    ->maxLength(255)->columnSpanFull(),
                Forms\Components\Hidden::make('teams_id')
                    ->default(auth()->user()->teams()->first()->id ?? null),

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
                    ->sortable()
                    ->formatStateUsing(function ($state) {
                        $expiredDate = Carbon::parse($state);
                        $now = Carbon::now();
    
                        if ($expiredDate->isPast()) {
                            return "<span style='color: red; font-weight: bold;'>Expired: {$expiredDate->format('Y-m-d')}</span>";
                        } else {
                            return $expiredDate->format('Y-m-d');
                        }
                    })
                    ->html(),
                // Tables\Columns\TextColumn::make('scope')->label('Scope')
                //     ->searchable(),
                Tables\Columns\TextColumn::make('external link')->label('External Link')
                    ->searchable()
                    ->formatStateUsing(function ($state) {
                        // Memastikan URL memiliki protokol. Jika tidak, tambahkan 'https://'
                        $url = preg_match('/^https?:\/\//', $state) ? $state : "https://{$state}";
                        
                        // Mengembalikan tag <a> dengan atribut target untuk membuka di tab baru
                        return "<a href='{$url}' target='_blank' style='padding:5px 10px; background-color:#ff8303; color:white; border-radius:5px;' rel='noopener noreferrer'>Click Here</a>";

                    })
                    ->html(),
                // Tables\Columns\TextColumn::make('description')->label('Description')
                //     ->searchable(),
                    //Belum bisa Link ke Document
                    Tables\Columns\TextColumn::make('doc')
                    ->label('Document')
                    ->formatStateUsing(fn ($state) => "<a href='/storage/{$state}' target='_blank' rel='noopener noreferrer' style='padding:5px 10px; background-color:#ff8303; color:white; border-radius:5px;'>Open Document</a>")
                    ->html()
                    ->searchable(),
                
                // Tables\Columns\TextColumn::make('customers.name')->label('Customer')
                //     ->numeric()
                //     ->url(fn($record) => $record->customer_id ? route('filament.admin.resources.customers.index', [
                //         'tenant' => Auth()->user()->teams()->first()->id,
                //         'record' => $record->customer_id,
                //     ]): null)->color(Color::Blue)
                //     ->sortable(),
                Tables\Columns\TextColumn::make('project.case')->label('Project')
                    ->numeric()
                    ->url(fn($record) => $record->project_id ?  route('filament.admin.resources.projects.index', [
                        'tenant' => Auth()->user()->teams()->first()->id,
                        'record' => $record->project_id,
                    ]): null)->color(Color::Blue)
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
                Tables\Filters\SelectFilter::make('scope')
                ->options([
                    'Flight' => 'Flight',
                    'Organization' => 'Organization',
                    'Pilot' => 'Pilot',
                    'Project' => 'Project',
                    'Drones' => 'Drones',
                    'Equidments/Battry' => 'Equidments/Battry',
                    'Incident' => 'Incident',
                ])
                ->label('Filter by Scope'),
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
            Section::make('Document Overview')
            ->schema([
                TextEntry::make('name')->label('Name'),
                TextEntry::make('refnumber')->label('REG Number'),
                TextEntry::make('users.name')->label('Owner'),
                TextEntry::make('type')->label('Type'),
                TextEntry::make('expired_date')->label('Expired Date')
                    ->date('Y-m-d')
                    ->formatStateUsing(function ($state) {
                        $expiredDate = Carbon::parse($state);
                        $now = Carbon::now();
    
                        if ($expiredDate->isPast()) {
                            return "<span style='color: red; font-weight: bold;'>Expired: {$expiredDate->format('Y-m-d')}</span>";
                        } else {
                            return $expiredDate->format('Y-m-d');
                        }
                    })
                    ->html(),
                TextEntry::make('scope')->label('Scope'),
                TextEntry::make('external link')->label('External Link')
                    ->formatStateUsing(function ($state) {
                        $url = preg_match('/^https?:\/\//', $state) ? $state : "https://{$state}";
                        return "<a href='{$url}' target='_blank' style='padding:5px 10px; background-color:#ff8303; color:white; border-radius:5px;' rel='noopener noreferrer'>Click Here</a>";

                    })
                    ->html(),
                TextEntry::make('doc')
                    ->label('Document')
                    ->formatStateUsing(fn ($state) => "<a href='/storage/{$state}' target='_blank' rel='noopener noreferrer' style='padding:5px 10px; background-color:#ff8303; color:white; border-radius:5px;'>Open Document</a>")
                    ->html(),
                TextEntry::make('description')->label('Description'),
                TextEntry::make('customers.name')->label('Customer')
                    ->url(fn($record) => $record->customer_id ? route('filament.admin.resources.customers.index', [
                        'tenant' => Auth()->user()->teams()->first()->id,
                        'record' => $record->customer_id,
                    ]): null)->color(Color::Blue),
                TextEntry::make('project.case')->label('Project')
                    ->url(fn($record) => $record->project_id ?  route('filament.admin.resources.projects.index', [
                        'tenant' => Auth()->user()->teams()->first()->id,
                        'record' => $record->project_id,
                    ]): null)->color(Color::Blue),
            ])->columns(3)

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
            // 'view' => Pages\ViewDocument::route('/{record}'),
            'edit' => Pages\EditDocument::route('/{record}/edit'),
        ];
    }
}
