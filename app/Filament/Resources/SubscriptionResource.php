<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubscriptionResource\Pages;
use App\Filament\Resources\SubscriptionResource\RelationManagers\UsagesRelationManager;
use App\Models\Subscription;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\Section as InfoListSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Table;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class SubscriptionResource extends Resource
{
    protected static ?string $model = Subscription::class;

    protected static ?int $navigationSort = 30;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $modelLabel = 'bérlet';

    protected static ?string $pluralModelLabel = 'bérletek';

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery();

        if (auth()->user()->owner !== true) {
            $query->where('user_id', '=', auth()->id());
        }

        return $query;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('')
                    ->schema([
                        Select::make('plan_id')
                            ->label('Bérlet típus')
                            ->relationship(name: 'plan', titleAttribute: 'name')
                            ->required()
                            ->searchable()
                            ->disabled(fn (Subscription $subscription) => $subscription->usages_count)
                            ->preload(),

                        Select::make('user_id')
                            ->label('Warrior')
                            ->relationship(name: 'user', titleAttribute: 'name')
                            ->required()
                            ->searchable()
                            ->disabled(fn (Subscription $subscription) => $subscription->usages_count)
                            ->preload(),

                        DatePicker::make('purchased_at')
                            ->label('Megvásárolva')
                            ->required()
                            ->disabled(fn (Subscription $subscription) => $subscription->usedUp())
                            ->default(Carbon::now()),
                    ]),

                Section::make('Meta adatok')
                    ->columns(['lg' => 2])
                    ->visible(fn (Subscription $subscription) => $subscription->exists)
                    ->schema([
                        Placeholder::make('Létrehozva')
                            ->content(fn (Subscription $subscription) => $subscription->created_at)
                            ->helperText(fn (Subscription $subscription) => $subscription->created_at->longRelativeToNowDiffForHumans()),

                        Placeholder::make('Módosítva')
                            ->content(fn (Subscription $subscription) => $subscription->updated_at)
                            ->helperText(fn (Subscription $subscription) => $subscription->updated_at->longRelativeToNowDiffForHumans()),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        $isAdmin = auth()->user()->owner === true;

        if ($isAdmin) {
            $table
                ->defaultGroup('user.name')
                ->groupingSettingsInDropdownOnDesktop()
                ->groups([
                    Tables\Grouping\Group::make('user.name')
                        ->label('Warrior'),

                    Tables\Grouping\Group::make('plan.name')
                        ->label('Bérlet típus'),
                ]);
        }

        return $table
            ->columns([
                ...($isAdmin ? [
                    ImageColumn::make('user.avatar_url')
                        ->defaultImageUrl('/storage/avatar.png')
                        ->grow(false)
                        ->label('')
                        ->size(40)
                        ->circular(),

                    Tables\Columns\TextColumn::make('user.name')
                        ->label('Warrior')
                        ->searchable()
                        ->sortable(),
                ] : []),

                Tables\Columns\TextColumn::make('plan.name')
                    ->label('Típus')
                    ->sortable(),

                Tables\Columns\TextColumn::make('purchased_at')
                    ->label('Megvásárolva')
                    ->date('Y-m-d')
                    ->sortable(),

                Tables\Columns\TextColumn::make('usages_count')
                    ->label('Edzések')
                    ->numeric()
                    ->alignRight()
                    ->sortable(),

                Tables\Columns\IconColumn::make('expired')
                    ->label('Lejárt')
                    ->boolean(),
            ])
            ->defaultSort('purchased_at', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('expired_at')
                    ->label('Aktív bérletek')
                    ->nullable()
                    ->trueLabel('Csak aktív bérletek')
                    ->falseLabel('Csak lejárt bérletek')
                    ->placeholder('Minden')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNull('expired_at'),
                        false: fn (Builder $query) => $query->whereNotNull('expired_at'),
                    ),

                ...($isAdmin ? [
                    Tables\Filters\SelectFilter::make('user')
                        ->relationship('user', 'name')
                        ->label('Warrior')
                        ->searchable()
                        ->preload(),
                ] : []),

                Tables\Filters\SelectFilter::make('plan')
                    ->relationship('plan', 'name')
                    ->label('Bérlet típus')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('expire_subscription')
                        ->label('Lejárt')
                        ->icon('heroicon-o-archive-box')
                        ->color('warning')
                        ->visible(fn (Subscription $subscription) => $isAdmin && ! $subscription->expired)
                        ->disabled(fn () => ! $isAdmin)
                        ->action(fn (Subscription $subscription) => $subscription->update(['expired_at' => Carbon::now()])),

                    Tables\Actions\Action::make('reactivate_subscription')
                        ->label('Visszaállítás')
                        ->icon('heroicon-o-arrow-path')
                        ->color('info')
                        ->visible(fn (Subscription $subscription) => $isAdmin && $subscription->expired)
                        ->disabled(fn (Subscription $subscription) => ! $isAdmin || $subscription->usedUp())
                        ->action(fn (Subscription $subscription) => $subscription->update(['expired_at' => null])),

                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    BulkAction::make('bulk_expire_subscription')
                        ->label('Lejárt')
                        ->modalSubmitActionLabel('Igen')
                        ->requiresConfirmation()
                        ->icon('heroicon-o-archive-box')
                        ->color('warning')
                        ->visible(fn () => $isAdmin)
                        ->disabled(fn () => ! $isAdmin)
                        ->action(fn (Subscription $subscription) => $subscription->update(['expired_at' => Carbon::now()])),

                    BulkAction::make('bulk_reactivate_subscription')
                        ->label('Visszaállítás')
                        ->modalSubmitActionLabel('Igen')
                        ->requiresConfirmation()
                        ->icon('heroicon-o-arrow-path')
                        ->color('info')
                        ->visible(fn () => $isAdmin)
                        ->disabled(fn () => ! $isAdmin)
                        ->action(fn (Subscription $subscription) => $subscription->update(['expired_at' => null])),

                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            InfoListSection::make()
                ->schema([
                    Grid::make(2)->schema([
                        Group::make([
                            TextEntry::make('plan.name')
                                ->label('Típus'),

                            TextEntry::make('purchased_at')
                                ->label('Megvásárolva')
                                ->date('Y-m-d'),
                        ]),

                        Group::make([
                            TextEntry::make('usages_count')
                                ->label('Felhasznált alkalmak')
                                ->numeric(),

                            IconEntry::make('expired')
                                ->label('Lejárt')
                                ->boolean(),
                        ]),
                    ]),
                ]),
        ]);
    }

    public static function getRelations(): array
    {
        return [
            UsagesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSubscriptions::route('/'),
            'create' => Pages\CreateSubscription::route('/create'),
            'edit' => Pages\EditSubscription::route('/{record}/edit'),
            'view' => Pages\ViewSubscription::route('/{record}'),
        ];
    }
}
