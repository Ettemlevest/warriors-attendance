<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TrainingResource\Pages;
use App\Filament\Resources\TrainingResource\RelationManagers\AttendeesRelationManager;
use App\Models\Training;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TrainingResource extends Resource
{
    protected static ?string $model = Training::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $modelLabel = 'edzés';

    protected static ?string $pluralModelLabel = 'edzések';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('')
                    ->columns(['lg' => 2])
                    ->schema([
                        Select::make('type')
                            ->label('Típus')
                            ->default('easy')
                            ->columnSpanFull()
                            ->required()
                            ->options([
                                'easy' => 'Felzárkózatató edzés',
                                'running' => 'Futó edzés',
                                'hard' => 'Haladó edzés',
                                'other' => 'Egyéb',
                            ]),

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
                            ->default(32)
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
                    ->visible(fn (Training $training) => $training->exists)
                    ->schema([
                        Placeholder::make('Létrehozva')
                            ->content(fn (Training $training) => $training->created_at)
                            ->helperText(fn (Training $training) => $training->created_at->longRelativeToNowDiffForHumans()),

                        Placeholder::make('Módosítva')
                            ->content(fn (Training $training) => $training->updated_at)
                            ->helperText(fn (Training $training) => $training->updated_at->longRelativeToNowDiffForHumans()),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        $trainingTypeIconGenerationCallback = fn (Training $training) => match ($training->type) {
            'easy' => 'heroicon-o-arrow-trending-up',
            'running' => 'heroicon-o-sparkles',
            'hard' => 'heroicon-o-bolt',
            'other' => 'heroicon-o-swatch',
            default => '',
        };

        return $table
            ->columns([
                IconColumn::make('type')
                    ->label('Típus')
                    ->color(fn (string $state) => match ($state) {
                        'easy' => 'primary',
                        'running' => 'warning',
                        'hard' => 'danger',
                        'other' => 'info',
                        default => '',
                    })
                    ->tooltip(fn (Training $training) => match ($training->type) {
                        'easy' => 'Felzárkóztató',
                        'running' => 'Futó',
                        'hard' => 'Haladó',
                        'other' => 'Egyéb',
                        default => 'Ismeretlen',
                    })
                    ->icon($trainingTypeIconGenerationCallback),

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
                    ->formatStateUsing(fn (string $state, Training $training): string => $state.' / '.($training->max_attendees !== 0 ? $training->max_attendees : '&infin;'))
                    ->html()
                    ->alignRight()
                    ->sortable(),

                ImageColumn::make('attendees.user.avatar_url')
                    ->label('')
                    ->stacked()
                    ->limitedRemainingText()
                    ->limit(5)
                    ->size(40)
                    ->circular(),
            ])
            ->defaultSort('start_at', 'desc')
            ->filters([
                SelectFilter::make('type')
                    ->label('Típus')
                    ->options([
                        'easy' => 'Felzárkóztató',
                        'running' => 'Futó',
                        'hard' => 'Haladó',
                        'other' => 'Egyéb',
                    ]),

                TernaryFilter::make('is_attendee')
                    ->label('Jelentkeztem')
                    ->nullable()
                    ->queries(
                        true: fn (Builder $query) => $query->whereHas(
                            'attendees',
                            fn (Builder $attendeesSubQuery) => $attendeesSubQuery->where('user_id', '=', 1)
                        ),
                        false: fn (Builder $query) => $query->whereDoesntHave(
                            'attendees',
                            fn (Builder $attendeesSubQuery) => $attendeesSubQuery->where('user_id', '=', 1)
                        ),
                    ),

            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            AttendeesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTrainings::route('/'),
            'create' => Pages\CreateTraining::route('/create'),
            'edit' => Pages\EditTraining::route('/{record}/edit'),
        ];
    }
}
