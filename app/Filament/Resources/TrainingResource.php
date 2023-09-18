<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TrainingResource\Pages;
use App\Models\Training;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
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
                //
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
                Tables\Columns\IconColumn::make('type')
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
                    ->sortable(),

                TextColumn::make('start_at')
                    ->label('Kezdés')
                    ->date('Y-m-d')
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
            'index' => Pages\ListTrainings::route('/'),
            'create' => Pages\CreateTraining::route('/create'),
            'edit' => Pages\EditTraining::route('/{record}/edit'),
        ];
    }
}
