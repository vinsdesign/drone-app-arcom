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
use Filament\Notifications\Notification;
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
use App\Helpers\TranslationHelper;

class DocumentResource extends Resource
{
    protected static ?string $model = Document::class;

    protected static ?string $navigationIcon = 'heroicon-m-document-text';
    public static ?string $tenantOwnershipRelationshipName = 'teams';

    public static ?int $navigationSort = 3;
    public static ?string $navigationGroup = ' ';
    protected static bool $isLazy = false;

    public static function getNavigationBadge(): ?string{
        $teamID = Auth()->user()->teams()->first()->id;
        return static::getModel()::Where('teams_id',$teamID)->where('status_visible', '!=', 'archived')->count();
    }

    public static function getNavigationLabel(): string
    {
        return TranslationHelper::translateIfNeeded('Documents');
    }
    public static function getModelLabel(): string
    {
        return TranslationHelper::translateIfNeeded('Documents');
    }

    public static function form(Form $form): Form
    {
        $currentTeamId = auth()->user()->teams()->first()->id;
        return $form
            ->schema([
                Forms\Components\Section::make('')
                ->description('')
                ->schema([
                    Forms\Components\TextInput::make('name')
                    ->label(TranslationHelper::translateIfNeeded('Name'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('type')
                    ->label(TranslationHelper::translateIfNeeded('Type'))                   
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
                Forms\Components\TextInput::make('refnumber')
                    ->label(TranslationHelper::translateIfNeeded('REF. Number'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\DatePicker::make('expired_date')
                    ->label(TranslationHelper::translateIfNeeded('Expired Date'))
                    ->required(),
                Forms\Components\Select::make('customers_id')
                    ->label(TranslationHelper::translateIfNeeded('Customer'))
                    // ->relationship('customers', 'name', function (Builder $query){
                    //     $currentTeamId = auth()->user()->teams()->first()->id;
                    //     $query->where('teams_id', $currentTeamId);
                    // }),
                    ->options(function (callable $get) use ($currentTeamId) {
                        return customer::where('teams_id', $currentTeamId)->pluck('name', 'id');
                    })
                    ->searchable(),
                Forms\Components\Select::make('scope')
                    ->label(TranslationHelper::translateIfNeeded('Scope'))
                    ->required()
                    ->options([
                        'Flight' => 'Flight',
                        'Organization' => 'Organization',
                        'Pilot' => 'Pilot',
                        'Project' => 'Project',
                        'Drones' => 'Drones',
                        'Equipments/Battery' => 'Equipments/Battery',
                        'Incident' => 'Incident',
                    ])
                    ->columnSpan(2),
                Forms\Components\Select::make('users_id')
                    ->label(TranslationHelper::translateIfNeeded('Owner'))
                    //->relationship('users', 'name')
                    ->options(function () {
                        $currentTeamId = auth()->user()->teams()->first()->id; 
                
                        return User::whereHas('teams', function (Builder $query) use ($currentTeamId) {
                            $query->where('team_user.team_id', $currentTeamId); 
                        })->pluck('name', 'id'); 
                    })
                    ->searchable()
                    ->required(),
                    Forms\Components\Select::make('projects_id')
                    ->label(TranslationHelper::translateIfNeeded('Project'))
                    // ->relationship('projects', 'case', function (Builder $query){
                    //     $currentTeamId = auth()->user()->teams()->first()->id;;
                    //     $query->where('teams_id', $currentTeamId);
                    // })
                    ->searchable()
                    ->options(function (callable $get) use ($currentTeamId) {
                        return projects::where('teams_id', $currentTeamId)
                        ->where('status_visible', '!=', 'archived')
                        ->pluck('case', 'id');
                    })
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('doc')
                    ->label(TranslationHelper::translateIfNeeded('Upload Document'))
                    ->acceptedFileTypes(['application/pdf']),
                Forms\Components\TextInput::make('external link')
                    ->label(TranslationHelper::translateIfNeeded('Or External Link, your document'))
                    ->required()
                    ->maxLength(255)->columnSpan(2),
                Forms\Components\TextArea::make('description')
                    ->label(TranslationHelper::translateIfNeeded('Description'))
                    ->maxLength(255)->columnSpanFull(),
                Forms\Components\Hidden::make('teams_id')
                    ->default(auth()->user()->teams()->first()->id ?? null),

                ])
                ->columns(3),
                
            ]);
    }
    //edit query untuk action shared un-shared
    // public static function getEloquentQuery(): Builder
    // {
    //     $userId = auth()->user()->id;
    //     $query = parent::getEloquentQuery();

    //     if (Auth()->user()->roles()->pluck('name')->contains('super_admin') || (Auth()->user()->roles()->pluck('name')->contains('panel_user'))) {
    //         return $query;
    //     }else{
    //         $query->where(function ($query) use ($userId) {
    //             $query->where('users_id', $userId);
    //         })
    //         ->orWhere(function ($query) use ($userId) {
    //             $query->where('users_id', '!=', $userId)->where('shared', 1);
    //         });
    //         return $query;
    //     }

    // }
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
                Tables\Columns\TextColumn::make('refnumber')
                    ->label(TranslationHelper::translateIfNeeded('Ref Number'))   
                    ->searchable(),
                Tables\Columns\TextColumn::make('users.name')
                    ->label(TranslationHelper::translateIfNeeded('Owner'))   
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label(TranslationHelper::translateIfNeeded('Type'))   
                    ->searchable(),
                Tables\Columns\TextColumn::make('expired_date')
                    ->label(TranslationHelper::translateIfNeeded('Expired Date'))   
                    ->date('Y-m-d')
                    ->sortable()
                    ->formatStateUsing(function ($state) {
                        $translatedText = (TranslationHelper::translateIfNeeded('Expired'));
                        $expiredDate = Carbon::parse($state);
                        $now = Carbon::now();
    
                        if ($expiredDate->isPast()) {
                            return "<span style='color: red; font-weight: bold;'>{$translatedText}: {$expiredDate->format('Y-m-d')}</span>";
                        } else {
                            return $expiredDate->format('Y-m-d');
                        }
                    })
                    ->html(),
                // Tables\Columns\TextColumn::make('scope')
                // ->label('Scope')// 
                    // ->searchable(),
                Tables\Columns\TextColumn::make('external link')
                    ->label(TranslationHelper::translateIfNeeded('External Link'))   
                    ->searchable()
                    ->formatStateUsing(function ($state) {
                        $translatedText = (TranslationHelper::translateIfNeeded('Click Here'));
                        $url = preg_match('/^https?:\/\//', $state) ? $state : "https://{$state}";
                        return "<a href='{$url}' target='_blank' style='padding:5px 10px; background-color:#ff8303; color:white; border-radius:5px;' rel='noopener noreferrer'>{$translatedText}</a>";

                    })
                    ->html()
                    ->placeholder(TranslationHelper::translateIfNeeded('No Link Uploaded')),
                // Tables\Columns\TextColumn::make('description')
                // ->label('Description')
                //     ->searchable(),
                    //Belum bisa Link ke Document
                    Tables\Columns\TextColumn::make('doc')
                        ->label(TranslationHelper::translateIfNeeded('Document')) 
                        ->formatStateUsing(fn ($state) => "<a href='/storage/{$state}' target='_blank' rel='noopener noreferrer' style='padding:5px 10px; background-color:#ff8303; color:white; border-radius:5px;'>Open Document</a>")
                        ->formatStateUsing(function ($state) {
                            $translatedText = (TranslationHelper::translateIfNeeded('Open Document'));
                            return "<a href='/storage/{$state}' target='_blank' rel='noopener noreferrer' style='padding:5px 10px; background-color:#ff8303; color:white; border-radius:5px;'>{$translatedText}</a>";
                        })
                        ->html()
                        ->searchable()
                        ->placeholder(TranslationHelper::translateIfNeeded('No Document Uploaded')),
                
                // Tables\Columns\TextColumn::make('customers.name')
                // ->label('Customer')
                //     ->numeric()
                //     ->url(fn($record) => $record->customer_id ? route('filament.admin.resources.customers.index', [
                //         'tenant' => Auth()->user()->teams()->first()->id,
                //         'record' => $record->customer_id,
                //     ]): null)->color(Color::Blue)
                //     ->sortable(),
                Tables\Columns\TextColumn::make('project.case')
                    ->label(TranslationHelper::translateIfNeeded('Project'))   
                    ->numeric()
                    ->url(fn($record) => $record->project_id ?  route('filament.admin.resources.projects.index', [
                        'tenant' => Auth()->user()->teams()->first()->id,
                        'record' => $record->project_id,
                    ]): null)->color(Color::Blue)
                    ->sortable()
                    ->placeholder(TranslationHelper::translateIfNeeded('No Project Selected')),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(TranslationHelper::translateIfNeeded('Created at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(TranslationHelper::translateIfNeeded('Updated at'))
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
                    'Equipments/Battery' => 'Equipments/Battery',
                    'Incident' => 'Incident',
                ])
                ->label(TranslationHelper::translateIfNeeded('Filter by Scope')),
                Tables\Filters\SelectFilter::make('status_visible')
                ->label('')
                ->options([
                    'current' => 'Current',
                    'archived' => 'Archived',
                ])
                ->default('current'),
                Tables\Filters\Filter::make('Locked')
                ->label(TranslationHelper::translateIfNeeded('Only Locked Document'))
                ->query(function ($query) {
                    $query->where('locked', '=', 'locked');
                }),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make()
                    ->hidden(fn ($record) => $record->locked === 'locked'),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\Action::make('Archive')->label(TranslationHelper::translateIfNeeded('Archive'))
                    ->hidden(fn ($record) => $record->status_visible == 'archived')
                            ->action(function ($record) {
                             $record->update(['status_visible' => 'archived']);
                             Notification::make()
                             ->title(TranslationHelper::translateIfNeeded('Status Updated'))
                             ->body(TranslationHelper::translateIfNeeded("Status successfully changed."))
                             ->success()
                             ->send();
                        })->icon('heroicon-s-archive-box-arrow-down'),
                    Tables\Actions\Action::make('Un-Archive')->label(TranslationHelper::translateIfNeeded(' Un-Archive'))
                    ->hidden(fn ($record) => $record->status_visible == 'current')
                            ->action(function ($record) {
                             $record->update(['status_visible' => 'current']);
                             Notification::make()
                             ->title(TranslationHelper::translateIfNeeded('Status Updated'))
                             ->body(TranslationHelper::translateIfNeeded("Status successfully changed."))
                             ->success()
                             ->send();
                        })->icon('heroicon-s-archive-box'),

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
                    Tables\Actions\Action::make('Lock')->label(TranslationHelper::translateIfNeeded('Lock'))
                        ->action(function ($record) {
                            $record->update(['locked' => 'locked']);
                            Notification::make()
                                ->title(TranslationHelper::translateIfNeeded('Data Locked'))
                                ->body(TranslationHelper::translateIfNeeded('This record is now locked and cannot be edited.'))
                                ->success()
                                ->send();
                        })
                        ->icon('heroicon-s-lock-closed')
                        ->hidden(fn ($record) => $record->locked === 'locked'), 
                    Tables\Actions\Action::make('Un-Lock')->label(TranslationHelper::translateIfNeeded('Unlock'))
                        ->action(function ($record) {
                            $record->update(['locked' => 'unlocked']);
                            Notification::make()
                                ->title(TranslationHelper::translateIfNeeded('Data Un-Locked'))
                                ->body(TranslationHelper::translateIfNeeded('This record is now unlocked and can be edited.'))
                                ->success()
                                ->send();
                        })
                        ->icon('heroicon-s-lock-open')
                        ->hidden(fn ($record) => $record->locked === null || $record->locked === 'unlocked')
                        ->visible(fn ($record) => auth()->user()->hasRole(['panel_user'])), 
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
            Section::make(TranslationHelper::translateIfNeeded('Document Overview'))
            ->schema([
                TextEntry::make('name')
                    ->label(TranslationHelper::translateIfNeeded('Name')),
                TextEntry::make('refnumber')
                    ->label(TranslationHelper::translateIfNeeded('Ref Number')),
                TextEntry::make('users.name')
                    ->label(TranslationHelper::translateIfNeeded('Owner')),
                TextEntry::make('type')
                    ->label(TranslationHelper::translateIfNeeded('Type')),
                TextEntry::make('expired_date')
                    ->label(TranslationHelper::translateIfNeeded('Expired Date'))
                    ->date('Y-m-d')
                    ->formatStateUsing(function ($state) {
                        $translatedText = (TranslationHelper::translateIfNeeded('Expired'));
                        $expiredDate = Carbon::parse($state);
                        $now = Carbon::now();
    
                        if ($expiredDate->isPast()) {
                            return "<span style='color: red; font-weight: bold;'>{$translatedText}: {$expiredDate->format('Y-m-d')}</span>";
                        } else {
                            return $expiredDate->format('Y-m-d');
                        }
                    })
                    ->html(),
                TextEntry::make('scope')
                    ->label(TranslationHelper::translateIfNeeded('Scope')),
                TextEntry::make('external link')
                    ->label(TranslationHelper::translateIfNeeded('External link'))
                    ->formatStateUsing(function ($state) {
                        $translatedText = (TranslationHelper::translateIfNeeded('Click Here'));
                        $url = preg_match('/^https?:\/\//', $state) ? $state : "https://{$state}";
                        return "<a href='{$url}' target='_blank' style='padding:5px 10px; background-color:#ff8303; color:white; border-radius:5px;' rel='noopener noreferrer'>{$translatedText}</a>";
                    })
                    ->html(),
                TextEntry::make('doc')
                    ->label(TranslationHelper::translateIfNeeded('Document'))
                    // ->formatStateUsing(fn ($state) => "<a href='/storage/{$state}' target='_blank' rel='noopener noreferrer' style='padding:5px 10px; background-color:#ff8303; color:white; border-radius:5px;'>Open Document</a>")
                    ->formatStateUsing(function ($state) {
                        $translatedText = (TranslationHelper::translateIfNeeded('Open Document'));
                        return "<a href='/storage/{$state}' target='_blank' rel='noopener noreferrer' style='padding:5px 10px; background-color:#ff8303; color:white; border-radius:5px;'>{$translatedText}</a>";
                    })
                    ->html(),
                TextEntry::make('description')
                    ->label(TranslationHelper::translateIfNeeded('Description')),
                TextEntry::make('customers.name')
                    ->label(TranslationHelper::translateIfNeeded('Customers'))
                    ->url(fn($record) => $record->customer_id ? route('filament.admin.resources.customers.index', [
                        'tenant' => Auth()->user()->teams()->first()->id,
                        'record' => $record->customer_id,
                    ]): null)->color(Color::Blue),
                TextEntry::make('project.case')
                    ->label(TranslationHelper::translateIfNeeded('Projects'))
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
    // untuk mengubah query bawaan filament
    // protected function getTableQuery(): Builder
    // {
    //     return parent::getTableQuery()
    //         ->where('status_visible', '!=','archived');
    // }

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
