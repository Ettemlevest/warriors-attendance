<?php

namespace App\Filament\Resources\TrainingResource\RelationManagers;

use App\Models\TrainingAttendance;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class AttendeesRelationManager extends RelationManager
{
    protected static string $relationship = 'attendees';

    protected static ?string $modelLabel = 'jelentkezés';

    protected static ?string $pluralModelLabel = 'jelentkezések';

    protected static ?string $title = 'Jelentkezők';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('user_id')
                    ->label('Név')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->autofocus()
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                ImageColumn::make('user.avatar_url')
                    ->defaultImageUrl('/avatar.png')
                    ->grow(false)
                    ->label('')
                    ->size(40)
                    ->circular(),

                TextColumn::make('user.name')
                    ->label('Név')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('created_at')
                    ->sortable()
                    ->label('Jelentkezett'),

                IconColumn::make('attended')
                    ->label('Résztvett')
                    ->grow(false)
                    ->sortable()
                    ->boolean(),

                IconColumn::make('extra')
                    ->label('Extra')
                    ->grow(false)
                    ->sortable()
                    ->boolean(),
            ])
            ->defaultSort('user.name', 'asc')
            ->filters([
                Filter::make('attended')
                    ->label('Résztvett')
                    ->query(fn (Builder $query) => $query->where('attended', '=', true)),

                Filter::make('extra')
                    ->label('Extra jelentkezés')
                    ->query(fn (Builder $query) => $query->where('extra', '=', true)),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\Action::make('confirm_attendance')
                    ->label('Résztvett')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (TrainingAttendance $attendance) => ! $attendance->attended && auth()->user()->isAdmin())
                    ->action(fn (TrainingAttendance $attendance) => $attendance->toggleAttendance()),

                Tables\Actions\Action::make('not_attended')
                    ->label('Hiányzott')
                    ->visible(fn (TrainingAttendance $attendance) => $attendance->attended && auth()->user()->isAdmin())
                    ->icon('heroicon-o-minus-circle')
                    ->color('warning')
                    ->action(fn (TrainingAttendance $attendance) => $attendance->toggleAttendance()),

                Tables\Actions\DeleteAction::make()
                    ->modalHeading('Jelentkezés törlése'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    BulkAction::make('bulk_attendance_confirmation')
                        ->label('Résztvettek')
                        ->modalSubmitActionLabel('Igen')
                        ->requiresConfirmation()
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn (Collection $attendances) => $attendances->each->toggleAttendance()),

                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
