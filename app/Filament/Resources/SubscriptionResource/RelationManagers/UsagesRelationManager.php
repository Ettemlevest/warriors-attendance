<?php

namespace App\Filament\Resources\SubscriptionResource\RelationManagers;

use App\Enums\TrainingType;
use App\Models\TrainingAttendance;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsagesRelationManager extends RelationManager
{
    protected static string $relationship = 'usages';

    protected static ?string $title = 'Felhasznált alkalmak';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('subscription')
                    ->relationship('subscription', 'plan.name')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordUrl(fn (TrainingAttendance $attendance) => "/trainings/{$attendance->training_id}")
            ->columns([
                IconColumn::make('training.type')
                    ->label('Típus')
                    ->grow(false)
                    ->tooltip(fn (TrainingType $state) => $state->getLabel()),

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
