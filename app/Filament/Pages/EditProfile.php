<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Auth\EditProfile as BasicEditProfile;

class EditProfile extends BasicEditProfile
{
    public function form(Form $form): Form
    {
        return $form->schema([
            $this->getNameFormComponent(),
            $this->getEmailFormComponent(),

            $this->getPasswordFormComponent(),
            $this->getPasswordConfirmationFormComponent(),

            Fieldset::make('Baleset esetén értesítendő')
                ->columns(1)
                ->schema([
                    TextInput::make('safety_person')
                        ->label('Név')
                        ->maxLength(255),

                    TextInput::make('safety_phone')
                        ->label('Telefonszám')
                        ->maxLength(255),
                ]),

            Fieldset::make('Versenyre jelentkezéshez')->columns(1)->schema([
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
        ]);
    }
}
