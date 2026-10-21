<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages\CreateUser;
use App\Filament\Resources\UserResource\Pages\EditUser;
use App\Filament\Resources\UserResource\Pages\ListUsers;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?int $navigationSort = 100;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $modelLabel = 'Warrior';

    protected static ?string $pluralModelLabel = 'Warriorok';

    protected static string|\UnitEnum|null $navigationGroup = 'Admin';

    public static function cancelForceDeletionWhenSubscribed(User $user, Action $action): void
    {
        if ($user->subscriptions()->doesntExist()) {
            return;
        }

        Notification::make()
            ->title('A Warrior nem törölhető véglegesen')
            ->body('Bérletek tartoznak hozzá. Töröld előbb a bérleteit, vagy hagyd archiválva.')
            ->danger()
            ->send();

        $action->cancel();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('')
                    ->columns(['lg' => 2])
                    ->schema([
                        TextInput::make('name')
                            ->label('Név')
                            ->required()
                            ->maxLength(255)
                            ->autofocus(),

                        TextInput::make('email')
                            ->label('E-mail')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        TextInput::make('password')
                            ->label('Jelszó')
                            ->password()
                            ->revealable()
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->rule(Password::default(), fn (?string $state): bool => filled($state))
                            ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->suffixAction(
                                Action::make('generatePassword')
                                    ->label('Jelszó generálása')
                                    ->icon('heroicon-o-key')
                                    ->action(fn (Set $set) => $set('password', Str::password(16))),
                            ),
                    ]),

                Section::make('Információk versenyre jelentkezéshez')
                    ->columns(['lg' => 2])
                    ->schema([
                        FileUpload::make('avatar_url')
                            ->label('Fénykép')
                            ->disk('public')
                            ->directory('users'),

                        Select::make('size')
                            ->label('Póló méret')
                            ->options([
                                'S' => 'S',
                                'M' => 'M',
                                'L' => 'L',
                                'XL' => 'XL',
                                'XXL' => 'XXL',
                            ])
                            ->nullable(),

                        DatePicker::make('birth_date')
                            ->label('Születési dátum'),

                        TextInput::make('phone')
                            ->label('Telefonszám')
                            ->maxLength(255),

                        TextInput::make('address')
                            ->label('Lakcím')
                            ->columnSpanFull()
                            ->maxLength(255),
                    ]),

                Section::make('Baleset esetén értesítendő')
                    ->columns(2)
                    ->schema([
                        TextInput::make('safety_person')
                            ->label('Név')
                            ->maxLength(255),

                        TextInput::make('safety_phone')
                            ->label('Telefonszám')
                            ->maxLength(255),
                    ]),

                Section::make('Meta adatok')
                    ->columns(['lg' => 2])
                    ->hiddenOn('create')
                    ->schema([
                        Placeholder::make('Létrehozva')
                            ->content(fn (?User $record) => $record?->created_at)
                            ->helperText(fn (?User $record) => $record?->created_at?->longRelativeToNowDiffForHumans()),

                        Placeholder::make('Módosítva')
                            ->content(fn (?User $record) => $record?->updated_at)
                            ->helperText(fn (?User $record) => $record?->updated_at?->longRelativeToNowDiffForHumans()),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('avatar_url')
                    ->disk('public')
                    ->defaultImageUrl('/storage/avatar.png')
                    ->grow(false)
                    ->label('')
                    ->size(40)
                    ->circular(),

                TextColumn::make('name')
                    ->label('Név')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('E-mail')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('age')
                    ->label('Életkor')
                    ->suffix(' év')
                    ->numeric()
                    ->alignRight(),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
                ForceDeleteAction::make()
                    ->before(fn (User $record, Action $action) => static::cancelForceDeletionWhenSubscribed($record, $action)),
                RestoreAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
                ForceDeleteBulkAction::make()
                    ->before(function (Collection $users, Action $action): void {
                        /** @var Collection<int, User> $users */
                        $subscribed = $users->first(fn (User $user) => $user->subscriptions()->exists());

                        if ($subscribed === null) {
                            return;
                        }

                        static::cancelForceDeletionWhenSubscribed($subscribed, $action);
                    }),
                RestoreBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
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
