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
use Stichoza\GoogleTranslate\GoogleTranslate;

class DocumentResource extends Resource
{
    protected static ?string $model = Document::class;

    protected static ?string $navigationIcon = 'heroicon-m-document-text';
    public static ?string $tenantOwnershipRelationshipName = 'teams';

    public static ?int $navigationSort = 3;
    public static ?string $navigationGroup = ' ';
    protected static bool $isLazy = false;

    public static function getNavigationBadge(): ?string{
        return static::getModel()::count();
    }

    public static function getNavigationLabel(): string
    {
        return GoogleTranslate::trans('Documents', session('locale') ?? 'en');
    }
    public static function getModelLabel(): string
    {
        return GoogleTranslate::trans('Documents', session('locale') ?? 'en');
    }

    public static function form(Form $form): Form
    {
        $currentTeamId = auth()->user()->teams()->first()->id;
        return $form
            ->schema([
                Forms\Components\Section::make('')
                ->description('')
                ->schema([
                    Forms\Components\TextInput::make('name')->label(GoogleTranslate::trans('name', session('locale') ?? 'en'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('type')->label(GoogleTranslate::trans('type', session('locale') ?? 'en'))                    
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
                Forms\Components\TextInput::make('refnumber')->label(GoogleTranslate::trans('REF.Number', session('locale') ?? 'en'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\DatePicker::make('expired_date')->label(GoogleTranslate::trans('Expired Date', session('locale') ?? 'en'))
                    ->required(),
                Forms\Components\Select::make('customers_id')->label(GoogleTranslate::trans('Customer', session('locale') ?? 'en'))
                    // ->relationship('customers', 'name', function (Builder $query){
                    //     $currentTeamId = auth()->user()->teams()->first()->id;
                    //     $query->where('teams_id', $currentTeamId);
                    // }),
                    ->options(function (callable $get) use ($currentTeamId) {
                        return customer::where('teams_id', $currentTeamId)->pluck('name', 'id');
                    })
                    ->searchable(),
                Forms\Components\Select::make('scope')->label(GoogleTranslate::trans('Scope', session('locale') ?? 'en'))
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
                Forms\Components\Select::make('users_id')->label(GoogleTranslate::trans('Owner', session('locale') ?? 'en'))
                    //->relationship('users', 'name')
                    ->options(function () {
                        $currentTeamId = auth()->user()->teams()->first()->id; 
                
                        return User::whereHas('teams', function (Builder $query) use ($currentTeamId) {
                            $query->where('team_user.team_id', $currentTeamId); 
                        })->pluck('name', 'id'); 
                    })
                    ->searchable()
                    ->required(),
                    Forms\Components\Select::make('projects_id')->label(GoogleTranslate::trans('Project / Job Reference', session('locale') ?? 'en'))
                    // ->relationship('projects', 'case', function (Builder $query){
                    //     $currentTeamId = auth()->user()->teams()->first()->id;;
                    //     $query->where('teams_id', $currentTeamId);
                    // })
                    ->searchable()
                    ->options(function (callable $get) use ($currentTeamId) {
                        return projects::where('teams_id', $currentTeamId)->pluck('case', 'id');
                    })
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('doc')->label(GoogleTranslate::trans('Upload Document', session('locale') ?? 'en'))
                    ->acceptedFileTypes(['application/pdf']),
                Forms\Components\TextInput::make('external link')->label(GoogleTranslate::trans('Or External Link, your document', session('locale') ?? 'en'))
                    ->required()
                    ->maxLength(255)->columnSpan(2),
                Forms\Components\TextArea::make('description')->label(GoogleTranslate::trans('Description', session('locale') ?? 'en'))
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
                Tables\Columns\TextColumn::make('name')->label(GoogleTranslate::trans('Name', session('locale') ?? 'en'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('refnumber')->label(GoogleTranslate::trans('REF. Number', session('locale') ?? 'en'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('users.name')->label(GoogleTranslate::trans('Owner', session('locale') ?? 'en'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')->label(GoogleTranslate::trans('Type', session('locale') ?? 'en'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('expired_date')->label(GoogleTranslate::trans('Expired Date', session('locale') ?? 'en'))
                    ->date('Y-m-d')
                    ->sortable()
                    ->formatStateUsing(function ($state) {
                        $translatedText = (new GoogleTranslate(session('locale') ?? 'en'))->translate('Expired');
                        $expiredDate = Carbon::parse($state);
                        $now = Carbon::now();
    
                        if ($expiredDate->isPast()) {
                            return "<span style='color: red; font-weight: bold;'>{$translatedText}: {$expiredDate->format('Y-m-d')}</span>";
                        } else {
                            return $expiredDate->format('Y-m-d');
                        }
                    })
                    ->html(),
                // Tables\Columns\TextColumn::make('scope')->label('Scope')
                //     ->searchable(),
                Tables\Columns\TextColumn::make('external link')->label(GoogleTranslate::trans('External Link', session('locale') ?? 'en'))
                    ->searchable()
                    ->formatStateUsing(function ($state) {
                        $translatedText = (new GoogleTranslate(session('locale') ?? 'en'))->translate('Click Here');
                        $url = preg_match('/^https?:\/\//', $state) ? $state : "https://{$state}";
                        return "<a href='{$url}' target='_blank' style='padding:5px 10px; background-color:#ff8303; color:white; border-radius:5px;' rel='noopener noreferrer'>{$translatedText}</a>";

                    })
                    ->html(),
                // Tables\Columns\TextColumn::make('description')->label('Description')
                //     ->searchable(),
                    //Belum bisa Link ke Document
                    Tables\Columns\TextColumn::make('doc')
                    ->label(GoogleTranslate::trans('Document', session('locale') ?? 'en'))
                    // ->formatStateUsing(fn ($state) => "<a href='/storage/{$state}' target='_blank' rel='noopener noreferrer' style='padding:5px 10px; background-color:#ff8303; color:white; border-radius:5px;'>Open Document</a>")
                    ->formatStateUsing(function ($state) {
                        $translatedText = (new GoogleTranslate(session('locale') ?? 'en'))->translate('Open Document');
                        return "<a href='/storage/{$state}' target='_blank' rel='noopener noreferrer' style='padding:5px 10px; background-color:#ff8303; color:white; border-radius:5px;'>{$translatedText}</a>";
                    })
                    ->html()
                    ->searchable(),
                
                // Tables\Columns\TextColumn::make('customers.name')->label('Customer')
                //     ->numeric()
                //     ->url(fn($record) => $record->customer_id ? route('filament.admin.resources.customers.index', [
                //         'tenant' => Auth()->user()->teams()->first()->id,
                //         'record' => $record->customer_id,
                //     ]): null)->color(Color::Blue)
                //     ->sortable(),
                Tables\Columns\TextColumn::make('project.case')->label(GoogleTranslate::trans('Projects', session('locale') ?? 'en'))
                    ->numeric()
                    ->url(fn($record) => $record->project_id ?  route('filament.admin.resources.projects.index', [
                        'tenant' => Auth()->user()->teams()->first()->id,
                        'record' => $record->project_id,
                    ]): null)->color(Color::Blue)
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(GoogleTranslate::trans('created_at', session('locale') ?? 'en'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(GoogleTranslate::trans('updated_at', session('locale') ?? 'en'))
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
                Tables\Filters\SelectFilter::make('status_visible')
                ->label('')
                ->options([
                    'current' => 'Current',
                    'archived' => 'Archived',
                ])
                ->default('current'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\Action::make('Archive')->label('Archive')
                    ->hidden(fn ($record) => $record->status_visible == 'archived')
                            ->action(function ($record) {
                             $record->update(['status_visible' => 'archived']);
                             Notification::make()
                             ->title('Status Updated')
                             ->body("Status successfully changed.")
                             ->success()
                             ->send();
                        })->icon('heroicon-s-archive-box-arrow-down'),
                    Tables\Actions\Action::make('Un-Archive')->label(' Un-Archive')
                    ->hidden(fn ($record) => $record->status_visible == 'current')
                            ->action(function ($record) {
                             $record->update(['status_visible' => 'current']);
                             Notification::make()
                             ->title('Status Updated')
                             ->body("Status successfully changed.")
                             ->success()
                             ->send();
                        })->icon('heroicon-s-archive-box'),
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
            Section::make('Document Overview')
            ->schema([
                TextEntry::make('name')->label(GoogleTranslate::trans('Name', session('locale') ?? 'en')),
                TextEntry::make('refnumber')->label(GoogleTranslate::trans('REF Number', session('locale') ?? 'en')),
                TextEntry::make('users.name')->label(GoogleTranslate::trans('Owner', session('locale') ?? 'en')),
                TextEntry::make('type')->label(GoogleTranslate::trans('Type', session('locale') ?? 'en')),
                TextEntry::make('expired_date')->label(GoogleTranslate::trans('Expired Date', session('locale') ?? 'en'))
                    ->date('Y-m-d')
                    ->formatStateUsing(function ($state) {
                        $translatedText = (new GoogleTranslate(session('locale') ?? 'en'))->translate('Expired');
                        $expiredDate = Carbon::parse($state);
                        $now = Carbon::now();
    
                        if ($expiredDate->isPast()) {
                            return "<span style='color: red; font-weight: bold;'>{$translatedText}: {$expiredDate->format('Y-m-d')}</span>";
                        } else {
                            return $expiredDate->format('Y-m-d');
                        }
                    })
                    ->html(),
                TextEntry::make('scope')->label(GoogleTranslate::trans('Scope', session('locale') ?? 'en')),
                TextEntry::make('external link')->label(GoogleTranslate::trans('External Link', session('locale') ?? 'en'))
                    ->formatStateUsing(function ($state) {
                        $translatedText = (new GoogleTranslate(session('locale') ?? 'en'))->translate('Click Here');
                        $url = preg_match('/^https?:\/\//', $state) ? $state : "https://{$state}";
                        return "<a href='{$url}' target='_blank' style='padding:5px 10px; background-color:#ff8303; color:white; border-radius:5px;' rel='noopener noreferrer'>{$translatedText}</a>";
                    })
                    ->html(),
                TextEntry::make('doc')
                    ->label(GoogleTranslate::trans('Document', session('locale') ?? 'en'))
                    // ->formatStateUsing(fn ($state) => "<a href='/storage/{$state}' target='_blank' rel='noopener noreferrer' style='padding:5px 10px; background-color:#ff8303; color:white; border-radius:5px;'>Open Document</a>")
                    ->formatStateUsing(function ($state) {
                        $translatedText = (new GoogleTranslate(session('locale') ?? 'en'))->translate('Open Document');
                        return "<a href='/storage/{$state}' target='_blank' rel='noopener noreferrer' style='padding:5px 10px; background-color:#ff8303; color:white; border-radius:5px;'>{$translatedText}</a>";
                    })
                    ->html(),
                TextEntry::make('description')->label(GoogleTranslate::trans('Description', session('locale') ?? 'en')),
                TextEntry::make('customers.name')->label(GoogleTranslate::trans('Customer', session('locale') ?? 'en'))
                    ->url(fn($record) => $record->customer_id ? route('filament.admin.resources.customers.index', [
                        'tenant' => Auth()->user()->teams()->first()->id,
                        'record' => $record->customer_id,
                    ]): null)->color(Color::Blue),
                TextEntry::make('project.case')->label(GoogleTranslate::trans('Project', session('locale') ?? 'en'))
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
