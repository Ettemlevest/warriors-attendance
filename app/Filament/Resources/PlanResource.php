<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlanResource\Pages\CreatePlan;
use App\Filament\Resources\PlanResource\Pages\EditPlan;
use App\Filament\Resources\PlanResource\Pages\ListPlans;
use App\Models\Plan;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PlanResource extends Resource
{
    protected static ?string $model = Plan::class;

    protected static ?int $navigationSort = 20;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $modelLabel = 'bérlet típus';

    protected static ?string $pluralModelLabel = 'bérlet típusok';

    protected static string|\UnitEnum|null $navigationGroup = 'Admin';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
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
                    ->hiddenOn('create')
                    ->schema([
                        Placeholder::make('Létrehozva')
                            ->content(fn (?Plan $record) => $record?->created_at)
                            ->helperText(fn (?Plan $record) => $record?->created_at?->longRelativeToNowDiffForHumans()),

                        Placeholder::make('Módosítva')
                            ->content(fn (?Plan $record) => $record?->updated_at)
                            ->helperText(fn (?Plan $record) => $record?->updated_at?->longRelativeToNowDiffForHumans()),
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
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
                RestoreAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
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
            'index' => ListPlans::route('/'),
            'create' => CreatePlan::route('/create'),
            'edit' => EditPlan::route('/{record}/edit'),
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
