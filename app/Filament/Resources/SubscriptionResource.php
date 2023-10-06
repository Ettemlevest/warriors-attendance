<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubscriptionResource\Pages;
use App\Models\Subscription;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
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

    protected static ?string $modelLabel = 'megvásárolt bérlet';

    protected static ?string $pluralModelLabel = 'megvásárolt bérletek';

    protected static ?string $navigationGroup = 'Admin';

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
                            ->preload(),

                        Select::make('user_id')
                            ->label('Warrior')
                            ->relationship(name: 'user', titleAttribute: 'name')
                            ->required()
                            ->searchable()
                            ->preload(),

                        DatePicker::make('purchased_at')
                            ->label('Megvásárolva')
                            ->required()
                            ->default(Carbon::now()),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('user.avatar_url')
                    ->defaultImageUrl('/avatar.png')
                    ->grow(false)
                    ->label('')
                    ->size(40)
                    ->circular(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Warrior')
                    ->sortable(),

                Tables\Columns\TextColumn::make('plan.name')
                    ->label('Bérlet típus')
                    ->sortable(),

                Tables\Columns\TextColumn::make('usages_count')
                    ->label('Felhasználva')
                    ->numeric()
                    ->alignRight()
                    ->sortable(),

                Tables\Columns\TextColumn::make('purchased_at')
                    ->label('Megvásárolva')
                    ->date('Y-m-d')
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

                Tables\Filters\SelectFilter::make('user')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('plan')
                    ->relationship('plan', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\Action::make('expire_subscription')
                    ->label('Lejárt')
                    ->icon('heroicon-o-archive-box')
                    ->color('warning')
                    ->visible(fn (Subscription $subscription) => $subscription->expired_at === null)
                    ->action(fn (Subscription $subscription) => $subscription->update(['expired_at' => Carbon::now()])),

                Tables\Actions\Action::make('reactivate_subscription')
                    ->label('Visszaállítás')
                    ->icon('heroicon-o-arrow-path')
                    ->color('info')
                    ->visible(fn (Subscription $subscription) => $subscription->expired_at !== null)
                    ->action(fn (Subscription $subscription) => $subscription->update(['expired_at' => null])),

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    BulkAction::make('bulk_expire_subscription')
                        ->label('Lejárt')
                        ->modalSubmitActionLabel('Igen')
                        ->requiresConfirmation()
                        ->icon('heroicon-o-archive-box')
                        ->color('warning')
                        ->action(fn (Subscription $subscription) => $subscription->update(['expired_at' => Carbon::now()])),

                    BulkAction::make('bulk_reactivate_subscription')
                        ->label('Visszaállítás')
                        ->modalSubmitActionLabel('Igen')
                        ->requiresConfirmation()
                        ->icon('heroicon-o-arrow-path')
                        ->color('info')
                        ->action(fn (Subscription $subscription) => $subscription->update(['expired_at' => null])),

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
            'index' => Pages\ListSubscriptions::route('/'),
            'create' => Pages\CreateSubscription::route('/create'),
            'edit' => Pages\EditSubscription::route('/{record}/edit'),
        ];
    }
}
