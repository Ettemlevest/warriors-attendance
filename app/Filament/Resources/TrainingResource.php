<?php

namespace App\Filament\Resources;

use App\Enums\TrainingType;
use App\Filament\Resources\TrainingResource\Pages\CreateTraining;
use App\Filament\Resources\TrainingResource\Pages\EditTraining;
use App\Filament\Resources\TrainingResource\Pages\ListTrainings;
use App\Filament\Resources\TrainingResource\Pages\ViewTraining;
use App\Filament\Resources\TrainingResource\RelationManagers\AttendeesRelationManager;
use App\Models\Training;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TrainingResource extends Resource
{
    protected static ?string $model = Training::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $modelLabel = 'edzés';

    protected static ?string $pluralModelLabel = 'edzések';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('')
                    ->columns(['lg' => 2])
                    ->schema([
                        Select::make('type')
                            ->label('Típus')
                            ->default(TrainingType::Easy)
                            ->columnSpanFull()
                            ->required()
                            ->options(TrainingType::class),

                        TextInput::make('name')
                            ->label('Edzés neve')
                            ->required()
                            ->autofocus()
                            ->maxLength(255),

                        TextInput::make('place')
                            ->label('Helyszín')
                            ->required()
                            ->datalist(
                                Training::query()
                                    ->select('place')
                                    ->selectRaw('COUNT(*) as count')
                                    ->groupBy('place')
                                    ->orderByDesc('count')
                                    ->having('count', '>', 5)
                                    ->take(20)
                                    ->pluck('place')
                                    ->all()
                            )
                            ->maxLength(255),

                        DateTimePicker::make('start_at')
                            ->label('Kezdés időpontja')
                            ->seconds(false)
                            ->required(),

                        TextInput::make('length')
                            ->label('Időtartam')
                            ->numeric()
                            ->required()
                            ->default(60)
                            ->suffix('perc')
                            ->suffixIcon('heroicon-o-clock'),

                        TextInput::make('max_attendees')
                            ->label('Max. létszám')
                            ->helperText('0 esetén nincs létszámkorlát')
                            ->default(32)
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->suffix('fő')
                            ->suffixIcon('heroicon-o-users'),

                        Toggle::make('can_attend_more')
                            ->label('Maximális létszám túlléphető')
                            ->inline(false)
                            ->default(true),

                        RichEditor::make('description')
                            ->label('Leírás')
                            ->columnSpanFull(),
                    ]),

                Section::make('Meta adatok')
                    ->columns(['lg' => 2])
                    ->hiddenOn('create')
                    ->schema([
                        Placeholder::make('Létrehozva')
                            ->content(fn (?Training $record) => $record?->created_at)
                            ->helperText(fn (?Training $record) => $record?->created_at?->longRelativeToNowDiffForHumans()),

                        Placeholder::make('Módosítva')
                            ->content(fn (?Training $record) => $record?->updated_at)
                            ->helperText(fn (?Training $record) => $record?->updated_at?->longRelativeToNowDiffForHumans()),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                IconColumn::make('type')
                    ->label('Típus')
                    ->tooltip(fn (TrainingType $state) => $state->getLabel()),

                TextColumn::make('name')
                    ->label('Edzés')
                    ->description(fn (Training $training) => $training->place)
                    ->searchable()
                    ->sortable(),

                TextColumn::make('start_at')
                    ->label('Kezdés')
                    ->date('Y-m-d H:i')
                    ->description(fn (Training $training) => $training->start_at->longRelativeToNowDiffForHumans())
                    ->sortable(),

                TextColumn::make('length')
                    ->label('Időtartam')
                    ->formatStateUsing(fn (Training $training): string => "{$training->length} perc")
                    ->alignRight(),

                TextColumn::make('attendees_count')
                    ->label('Létszám')
                    ->formatStateUsing(fn (string $state, Training $training): string => $state.' / '.($training->hasUnlimitedCapacity() ? '&infin;' : $training->max_attendees))
                    ->html()
                    ->alignRight()
                    ->sortable(),
            ])
            ->defaultSort('start_at', 'desc')
            ->filters([
                SelectFilter::make('type')
                    ->label('Típus')
                    ->options(TrainingType::class),

                TernaryFilter::make('is_attendee')
                    ->label('Jelentkeztem')
                    ->nullable()
                    ->queries(
                        true: fn (Builder $query) => $query->whereHas(
                            'attendees',
                            fn (Builder $attendeesSubQuery) => $attendeesSubQuery->where('user_id', '=', auth()->id())
                        ),
                        false: fn (Builder $query) => $query->whereDoesntHave(
                            'attendees',
                            fn (Builder $attendeesSubQuery) => $attendeesSubQuery->where('user_id', '=', auth()->id())
                        ),
                    ),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()
                ->schema([
                    Grid::make(2)->schema([
                        Group::make([
                            TextEntry::make('type')
                                ->label('Típus'),

                            TextEntry::make('name')
                                ->label('Edzés'),

                            TextEntry::make('place')
                                ->label('Helyszín')
                                ->icon('heroicon-o-map-pin'),
                        ]),

                        Group::make([
                            TextEntry::make('start_at')
                                ->label('Kezdés időpontja'),

                            TextEntry::make('length')
                                ->label('Időtartam')
                                ->suffix(' perc')
                                ->icon('heroicon-o-clock'),
                        ]),
                    ])->inlineLabel(),
                ]),

            Section::make('Leírás')
                ->collapsible()
                ->schema([
                    TextEntry::make('description')
                        ->prose()
                        ->html()
                        ->hiddenLabel()
                        ->default('-'),
                ]),
        ]);
    }

    public static function getRelations(): array
    {
        return [
            AttendeesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTrainings::route('/'),
            'create' => CreateTraining::route('/create'),
            'edit' => EditTraining::route('/{record}/edit'),
            'view' => ViewTraining::route('/{record}'),
        ];
    }
}
