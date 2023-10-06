<?php

namespace App\Filament\Resources\SubscriptionResource\RelationManagers;

use App\Models\TrainingAttendance;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsagesRelationManager extends RelationManager
{
    protected static string $relationship = 'usages';

    protected static ?string $title = 'Felhasznált alkalmak';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('subscription')
                    ->relationship('subscription', 'plan.name')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        // TODO: move to the training model, use enum
        $trainingTypeIconGenerationCallback = fn (TrainingAttendance $attendance) => match ($attendance->training->type) {
            'easy' => 'heroicon-o-arrow-trending-up',
            'running' => 'heroicon-o-sparkles',
            'hard' => 'heroicon-o-bolt',
            'other' => 'heroicon-o-swatch',
            default => '',
        };

        return $table
            ->recordTitleAttribute('training.name')
            ->columns([
                TextColumn::make('training.type'),

                IconColumn::make('training.type')
                    ->label('Típus')
                    ->grow(false)
                    ->color(fn (string $state) => match ($state) {
                        'easy' => 'primary',
                        'running' => 'warning',
                        'hard' => 'danger',
                        'other' => 'info',
                        default => '',
                    })
                    ->tooltip(fn (TrainingAttendance $attendance) => match ($attendance->training->type) {
                        'easy' => 'Felzárkóztató',
                        'running' => 'Futó',
                        'hard' => 'Haladó',
                        'other' => 'Egyéb',
                        default => 'Ismeretlen',
                    })
                    ->icon($trainingTypeIconGenerationCallback),

                TextColumn::make('training.name')
                    ->label('Edzés')
                    ->description(fn (TrainingAttendance $attendance) => $attendance->training->place)
                    ->searchable()
                    ->sortable(),

                TextColumn::make('training.start_at')
                    ->label('Kezdés')
                    ->date('Y-m-d H:i')
                    ->description(fn (TrainingAttendance $attendance) => $attendance->training->start_at->longRelativeToNowDiffForHumans())
                    ->sortable(),

                TextColumn::make('training.length')
                    ->label('Időtartam')
                    ->formatStateUsing(fn (TrainingAttendance $attendance): string => "{$attendance->training->length} perc")
                    ->alignRight(),
            ])
            ->filters([
                //
            ]);
    }
}
