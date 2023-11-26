<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?int $navigationSort = 100;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $modelLabel = 'Warrior';

    protected static ?string $pluralModelLabel = 'Warriorok';

    protected static ?string $navigationGroup = 'Admin';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
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
                            ->maxLength(255),
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
                    ->visible(fn (User $user) => $user->exists)
                    ->schema([
                        Placeholder::make('Létrehozva')
                            ->content(fn (User $user) => $user->created_at)
                            ->helperText(fn (User $user) => $user->created_at->longRelativeToNowDiffForHumans()),

                        Placeholder::make('Módosítva')
                            ->content(fn (User $user) => $user->updated_at)
                            ->helperText(fn (User $user) => $user->updated_at->longRelativeToNowDiffForHumans()),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('avatar_url')
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
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\ForceDeleteBulkAction::make(),
                Tables\Actions\RestoreBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
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
