<?php

use App\Enums\TrainingType;

it('maps every type to a label, colour and icon', function (TrainingType $type, string $label, string $color, string $icon) {
    expect($type->getLabel())->toBe($label)
        ->and($type->getColor())->toBe($color)
        ->and($type->getIcon())->toBe($icon);
})->with([
    'easy' => [TrainingType::Easy, 'Felzárkóztató edzés', 'primary', 'heroicon-o-arrow-trending-up'],
    'running' => [TrainingType::Running, 'Futó edzés', 'warning', 'heroicon-o-sparkles'],
    'hard' => [TrainingType::Hard, 'Haladó edzés', 'danger', 'heroicon-o-bolt'],
    'other' => [TrainingType::Other, 'Egyéb', 'info', 'heroicon-o-swatch'],
]);

it('is backed by the stored string values', function () {
    expect(array_map(fn (TrainingType $type) => $type->value, TrainingType::cases()))
        ->toBe(['easy', 'running', 'hard', 'other']);
});
