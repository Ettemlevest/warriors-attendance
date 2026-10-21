<?php

namespace App\Filament\Widgets;

use App\Enums\TrainingType;
use App\Models\Training;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class UpcomingTraining extends BaseWidget
{
    protected static ?string $heading = 'Edzések a következő 30 napban';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 20;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Training::query()
                    ->withCount('attendees')
                    ->whereBetween('start_at', [now()->subHour(), now()->addDays(30)]),
            )
            ->defaultSort('start_at', 'asc')
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
            ->actionsPosition(ActionsPosition::BeforeColumns)
            ->recordUrl(fn (Training $training) => route('filament.admin.resources.trainings.view', ['record' => $training->id]))
            ->actions([
                Action::make('attend')
                    ->label('Jelentkezem')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Training $training) => ! $training->hasAttendee(auth()->user()))
                    ->disabled(fn (Training $training) => ! $training->isOpenForRegistration() || ! $training->canAcceptRegistration())
                    ->action(fn (Training $training) => $training->registerAttendee(auth()->user())),

                Action::make('withdraw')
                    ->label('Lemondom')
                    ->icon('heroicon-o-minus-circle')
                    ->color('warning')
                    ->visible(fn (Training $training) => $training->hasAttendee(auth()->user()))
                    ->disabled(fn (Training $training) => ! $training->isOpenForRegistration() || $training->attendanceOf(auth()->user())?->attended)
                    ->action(fn (Training $training) => $training->attendees()->where('user_id', '=', auth()->id())->delete()),
            ]);
    }
}
