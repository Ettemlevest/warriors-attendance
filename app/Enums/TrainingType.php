<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum TrainingType: string implements HasColor, HasIcon, HasLabel
{
    case Easy = 'easy';
    case Running = 'running';
    case Hard = 'hard';
    case Other = 'other';

    public function getLabel(): string
    {
        return match ($this) {
            self::Easy => 'Felzárkóztató edzés',
            self::Running => 'Futó edzés',
            self::Hard => 'Haladó edzés',
            self::Other => 'Egyéb',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Easy => 'primary',
            self::Running => 'warning',
            self::Hard => 'danger',
            self::Other => 'info',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::Easy => 'heroicon-o-arrow-trending-up',
            self::Running => 'heroicon-o-sparkles',
            self::Hard => 'heroicon-o-bolt',
            self::Other => 'heroicon-o-swatch',
        };
    }
}
