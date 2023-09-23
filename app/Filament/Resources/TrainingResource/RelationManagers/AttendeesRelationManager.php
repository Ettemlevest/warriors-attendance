<?php

namespace App\Filament\Resources\TrainingResource\RelationManagers;

use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Contracts\Database\Eloquent\Builder;

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
                TextColumn::make('user.name')
                    ->label('Név')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('created_at')
                    ->sortable()
                    ->label('Jelentkezett'),

                IconColumn::make('attended')
                    ->label('Résztvett')
                    ->sortable()
                    ->boolean(),

                IconColumn::make('extra')
                    ->label('Extra')
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
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
