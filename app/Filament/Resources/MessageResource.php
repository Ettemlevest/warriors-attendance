<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MessageResource\Pages;
use App\Models\Message;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MessageResource extends Resource
{
    protected static ?string $model = Message::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $modelLabel = 'üzenet';

    protected static ?string $pluralModelLabel = 'üzenetek';

    protected static ?string $navigationGroup = 'Admin';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('')->schema([
                    TextInput::make('title')
                        ->label('Cím')
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
                    ->visible(fn (Message $message) => $message->exists)
                    ->schema([
                        Placeholder::make('Létrehozva')
                            ->content(fn (Message $message) => $message->created_at)
                            ->helperText(fn (Message $message) => $message->created_at->longRelativeToNowDiffForHumans()),

                        Placeholder::make('Módosítva')
                            ->content(fn (Message $message) => $message->updated_at)
                            ->helperText(fn (Message $message) => $message->updated_at->longRelativeToNowDiffForHumans()),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Cím')
                    ->limit(50)
                    ->sortable(),

                Tables\Columns\TextColumn::make('showed_from')
                    ->label('Dátumtól')
                    ->date('Y-m-d')
                    ->sortable(),

                Tables\Columns\TextColumn::make('showed_to')
                    ->label('Dátumig')
                    ->date('Y-m-d')
                    ->sortable(),
            ])
            ->defaultSort('showed_from', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListMessages::route('/'),
            'create' => Pages\CreateMessage::route('/create'),
            'edit' => Pages\EditMessage::route('/{record}/edit'),
        ];
    }
}
