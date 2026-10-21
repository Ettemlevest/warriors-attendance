<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubscriptionResource\Pages\CreateSubscription;
use App\Filament\Resources\SubscriptionResource\Pages\EditSubscription;
use App\Filament\Resources\SubscriptionResource\Pages\ListSubscriptions;
use App\Filament\Resources\SubscriptionResource\Pages\ViewSubscription;
use App\Filament\Resources\SubscriptionResource\RelationManagers\UsagesRelationManager;
use App\Models\Subscription;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class SubscriptionResource extends Resource
{
    protected static ?string $model = Subscription::class;

    protected static ?int $navigationSort = 30;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $modelLabel = 'bérlet';

    protected static ?string $pluralModelLabel = 'bérletek';

    public static function getEloquentQuery(): EloquentBuilder
    {
        $query = parent::getEloquentQuery();

        if (auth()->user()->owner !== true) {
            $query->where('user_id', '=', auth()->id());
        }

        return $query;
    }

    public static function cancelDeletionWhenUsed(Subscription $subscription, Action $action): void
    {
        if ($subscription->usages()->doesntExist()) {
            return;
        }

        Notification::make()
            ->title('A bérlet nem törölhető')
            ->body('Vannak felhasznált alkalmai. Jelöld inkább lejártnak.')
            ->danger()
            ->send();

        $action->cancel();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('')
                    ->schema([
                        Select::make('plan_id')
                            ->label('Bérlet típus')
                            ->relationship(
                                name: 'plan',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn (EloquentBuilder $query) => $query->whereNull('deleted_at'),
                            )
                            ->required()
                            ->searchable()
                            ->disabled(fn (?Subscription $record): bool => (bool) $record?->usages_count)
                            ->preload(),

                        Select::make('user_id')
                            ->label('Warrior')
                            ->relationship(name: 'user', titleAttribute: 'name')
                            ->required()
                            ->searchable()
                            ->disabled(fn (?Subscription $record): bool => (bool) $record?->usages_count)
                            ->preload(),

                        DatePicker::make('purchased_at')
                            ->label('Megvásárolva')
                            ->required()
                            ->disabled(fn (?Subscription $record): bool => $record?->usedUp() ?? false)
                            ->default(Carbon::now()),
                    ]),

                Section::make('Meta adatok')
                    ->columns(['lg' => 2])
                    ->hiddenOn('create')
                    ->schema([
                        Placeholder::make('Létrehozva')
                            ->content(fn (?Subscription $record) => $record?->created_at)
                            ->helperText(fn (?Subscription $record) => $record?->created_at?->longRelativeToNowDiffForHumans()),

                        Placeholder::make('Módosítva')
                            ->content(fn (?Subscription $record) => $record?->updated_at)
                            ->helperText(fn (?Subscription $record) => $record?->updated_at?->longRelativeToNowDiffForHumans()),
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
                    Group::make('user.name')
                        ->label('Warrior'),

                    Group::make('plan.name')
                        ->label('Bérlet típus'),
                ]);
        }

        return $table
            ->columns([
                ...($isAdmin ? [
                    ImageColumn::make('user.avatar_url')
                        ->disk('public')
                        ->defaultImageUrl('/storage/avatar.png')
                        ->grow(false)
                        ->label('')
                        ->size(40)
                        ->circular(),

                    TextColumn::make('user.name')
                        ->label('Warrior')
                        ->searchable()
                        ->sortable(),
                ] : []),

                TextColumn::make('plan.name')
                    ->label('Típus')
                    ->sortable(),

                TextColumn::make('purchased_at')
                    ->label('Megvásárolva')
                    ->date('Y-m-d')
                    ->sortable(),

                TextColumn::make('usages_count')
                    ->label('Edzések')
                    ->numeric()
                    ->alignRight()
                    ->sortable(),

                IconColumn::make('expired')
                    ->label('Lejárt')
                    ->boolean(),
            ])
            ->defaultSort('purchased_at', 'desc')
            ->filters([
                TernaryFilter::make('expired_at')
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
                    SelectFilter::make('user')
                        ->relationship('user', 'name')
                        ->label('Warrior')
                        ->searchable()
                        ->preload(),
                ] : []),

                SelectFilter::make('plan')
                    ->relationship('plan', 'name')
                    ->label('Bérlet típus')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make('expire_subscription')
                        ->label('Lejárt')
                        ->icon('heroicon-o-archive-box')
                        ->color('warning')
                        ->visible(fn (Subscription $subscription) => $isAdmin && ! $subscription->expired)
                        ->disabled(fn () => ! $isAdmin)
                        ->action(fn (Subscription $subscription) => $subscription->update(['expired_at' => Carbon::now()])),

                    Action::make('reactivate_subscription')
                        ->label('Visszaállítás')
                        ->icon('heroicon-o-arrow-path')
                        ->color('info')
                        ->visible(fn (Subscription $subscription) => $isAdmin && $subscription->expired)
                        ->disabled(fn (Subscription $subscription) => ! $isAdmin || $subscription->usedUp())
                        ->action(fn (Subscription $subscription) => $subscription->update(['expired_at' => null])),

                    EditAction::make(),
                    DeleteAction::make()
                        ->before(fn (Subscription $subscription, Action $action) => static::cancelDeletionWhenUsed($subscription, $action)),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('bulk_expire_subscription')
                        ->label('Lejárt')
                        ->modalSubmitActionLabel('Igen')
                        ->requiresConfirmation()
                        ->icon('heroicon-o-archive-box')
                        ->color('warning')
                        ->visible(fn () => $isAdmin)
                        ->disabled(fn () => ! $isAdmin)
                        ->deselectRecordsAfterCompletion()
                        ->action(fn (Collection $subscriptions) => $subscriptions->each->update(['expired_at' => Carbon::now()])),

                    BulkAction::make('bulk_reactivate_subscription')
                        ->label('Visszaállítás')
                        ->modalSubmitActionLabel('Igen')
                        ->requiresConfirmation()
                        ->icon('heroicon-o-arrow-path')
                        ->color('info')
                        ->visible(fn () => $isAdmin)
                        ->disabled(fn () => ! $isAdmin)
                        ->deselectRecordsAfterCompletion()
                        ->action(function (Collection $subscriptions): void {
                            /** @var Collection<int, Subscription> $subscriptions */
                            $subscriptions
                                ->reject(fn (Subscription $subscription) => $subscription->usedUp())
                                ->each(fn (Subscription $subscription) => $subscription->update(['expired_at' => null]));
                        }),

                    DeleteBulkAction::make()
                        ->before(function (Collection $subscriptions, Action $action): void {
                            /** @var Collection<int, Subscription> $subscriptions */
                            $used = $subscriptions->first(fn (Subscription $subscription) => $subscription->usages()->exists());

                            if ($used === null) {
                                return;
                            }

                            static::cancelDeletionWhenUsed($used, $action);
                        }),
                ]),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()
                ->schema([
                    Grid::make(2)->schema([
                        \Filament\Schemas\Components\Group::make([
                            TextEntry::make('plan.name')
                                ->label('Típus'),

                            TextEntry::make('purchased_at')
                                ->label('Megvásárolva')
                                ->date('Y-m-d'),
                        ]),

                        \Filament\Schemas\Components\Group::make([
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
            'index' => ListSubscriptions::route('/'),
            'create' => CreateSubscription::route('/create'),
            'edit' => EditSubscription::route('/{record}/edit'),
            'view' => ViewSubscription::route('/{record}'),
        ];
    }
}
