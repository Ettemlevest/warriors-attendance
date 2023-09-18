<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TrainingResource\Pages;
use App\Models\Training;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

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
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Edzés')
                    ->sortable(),

                TextColumn::make('start_at')
                    ->date('Y-m-d')
                    ->label('Kezdés')
                    ->sortable(),
            ])
            ->defaultSort('start_at', 'desc')
            ->filters([
                //
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
