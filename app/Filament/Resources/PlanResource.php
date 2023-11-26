<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlanResource\Pages;
use App\Models\Plan;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PlanResource extends Resource
{
    protected static ?string $model = Plan::class;

    protected static ?int $navigationSort = 20;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $modelLabel = 'bérlet típus';

    protected static ?string $pluralModelLabel = 'bérlet típusok';

    protected static ?string $navigationGroup = 'Admin';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('')
                    ->columns(['lg' => 2])
                    ->schema([
                        TextInput::make('name')
                            ->label('Megnevezés')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->autofocus()
                            ->maxLength(255),

                        TextInput::make('sessions')
                            ->label('Felhasználható')
                            ->helperText('Ahány alkalomra jogosít')
                            ->suffix('alkalom')
                            ->required()
                            ->numeric()
                            ->default(10)
                            ->minValue(1)
                            ->maxValue(255),
                    ]),

                Section::make('Meta adatok')
                    ->columns(['lg' => 2])
                    ->visible(fn (Plan $plan) => $plan->exists)
                    ->schema([
                        Placeholder::make('Létrehozva')
                            ->content(fn (Plan $plan) => $plan->created_at)
                            ->helperText(fn (Plan $plan) => $plan->created_at->longRelativeToNowDiffForHumans()),

                        Placeholder::make('Módosítva')
                            ->content(fn (Plan $plan) => $plan->updated_at)
                            ->helperText(fn (Plan $plan) => $plan->updated_at->longRelativeToNowDiffForHumans()),
                    ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Megnevezés')
                    ->sortable(),

                TextColumn::make('sessions')
                    ->label('Alkalom')
                    ->numeric()
                    ->alignRight()
                    ->sortable(),

                TextColumn::make('subscriptions_count')
                    ->label('Megvásárolt bérletek')
                    ->numeric()
                    ->alignRight()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Létrehozva')
                    ->sortable(),
            ])
            ->defaultSort('name', 'asc')
            ->filters([
                TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
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
            'index' => Pages\ListPlans::route('/'),
            'create' => Pages\CreatePlan::route('/create'),
            'edit' => Pages\EditPlan::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
