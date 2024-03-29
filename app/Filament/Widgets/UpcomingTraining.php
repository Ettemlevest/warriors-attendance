<?php

namespace App\Filament\Widgets;

use App\Models\Training;
use Carbon\CarbonPeriod;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Carbon;

class UpcomingTraining extends BaseWidget
{
    protected static ?string $heading = 'Edzések a következő 30 napban';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 20;

    public function table(Table $table): Table
    {
        // TODO: move to the training model, use enum
        $trainingTypeIconGenerationCallback = fn (Training $training) => match ($training->type) {
            'easy' => 'heroicon-o-arrow-trending-up',
            'running' => 'heroicon-o-sparkles',
            'hard' => 'heroicon-o-bolt',
            'other' => 'heroicon-o-swatch',
            default => '',
        };

        return $table
            ->query(
                Training::query()
                    ->withCount('attendees')
                    ->whereBetween('start_at', CarbonPeriod::create('now', '+1 month')),
            )
            ->defaultSort('start_at', 'asc')
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
            ])
            ->actionsPosition(ActionsPosition::BeforeColumns)
            ->recordUrl(fn (Training $training) => route('filament.admin.resources.trainings.view', ['record' => $training->id]))
            ->actions([
                Action::make('attend')
                    ->label('Jelentkezem')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Training $training) => ! $training->hasAttendee(auth()->user()))
                    ->disabled(fn (Training $training) => $training->start_at->isBefore(Carbon::now()->subHour()) || $training->attendees_count >= $training->max_attendees && ! $training->can_attend_more)
                    ->action(fn (Training $training) => auth()->user()->trainingAttendances()->create(['training_id' => $training->id])),

                Action::make('withdraw')
                    ->label('Lemondom')
                    ->icon('heroicon-o-minus-circle')
                    ->color('warning')
                    ->visible(fn (Training $training) => $training->hasAttendee(auth()->user()))
                    ->disabled(fn (Training $training) => $training->start_at->isBefore(Carbon::now()->subHour()))
                    ->action(fn (Training $training) => $training->attendees()->where('user_id', '=', auth()->id())->delete()),
            ]);
    }
}
