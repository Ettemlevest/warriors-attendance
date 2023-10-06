<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\FileUpload;
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

            FileUpload::make('avatar_url')
                ->label('Profilkép')
                ->disk('public')
                ->directory('users'),

            TextInput::make('birth_date'),

            $this->getPasswordFormComponent(),
            $this->getPasswordConfirmationFormComponent(),
        ]);
    }
}
