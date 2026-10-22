<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MessageResource\Pages\CreateMessage;
use App\Filament\Resources\MessageResource\Pages\EditMessage;
use App\Filament\Resources\MessageResource\Pages\ListMessages;
use App\Models\Message;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MessageResource extends Resource
{
    protected static ?string $model = Message::class;

    protected static ?int $navigationSort = 10;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $modelLabel = 'üzenet';

    protected static ?string $pluralModelLabel = 'üzenetek';

    protected static string|\UnitEnum|null $navigationGroup = 'Admin';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('')
                    ->columns(['lg' => 2])
                    ->schema([
                        TextInput::make('title')
                            ->label('Cím')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        RichEditor::make('body')
                            ->label('Üzenet')
                            ->columnSpanFull(),

                        DatePicker::make('showed_from')
                            ->label('Dátumtól'),

                        DatePicker::make('showed_to')
                            ->label('Dátumig'),
                    ]),
                Section::make('Meta adatok')
                    ->columns(['lg' => 2])
                    ->hiddenOn('create')
                    ->schema([
                        Placeholder::make('Létrehozva')
                            ->content(fn (?Message $record) => $record?->created_at)
                            ->helperText(fn (?Message $record) => $record?->created_at?->longRelativeToNowDiffForHumans()),

                        Placeholder::make('Módosítva')
                            ->content(fn (?Message $record) => $record?->updated_at)
                            ->helperText(fn (?Message $record) => $record?->updated_at?->longRelativeToNowDiffForHumans()),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Cím')
                    ->limit(50)
                    ->searchable()
                    ->sortable(),

                TextColumn::make('showed_from')
                    ->label('Dátumtól')
                    ->date('Y-m-d')
                    ->sortable(),

                TextColumn::make('showed_to')
                    ->label('Dátumig')
                    ->date('Y-m-d')
                    ->sortable(),
            ])
            ->defaultSort('showed_from', 'desc')
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
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
            'index' => ListMessages::route('/'),
            'create' => CreateMessage::route('/create'),
            'edit' => EditMessage::route('/{record}/edit'),
        ];
    }
}
