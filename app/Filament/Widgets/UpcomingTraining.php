<?php

namespace App\Filament\Widgets;

use App\Models\Training;
use Carbon\CarbonPeriod;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class UpcomingTraining extends BaseWidget
{
    protected static ?string $heading = 'Közelgő edzések';

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
            ]);
    }
}
