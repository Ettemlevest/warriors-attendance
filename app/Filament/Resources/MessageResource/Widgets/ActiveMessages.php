<?php

namespace App\Filament\Resources\MessageResource\Widgets;

use App\Models\Message;
use Filament\Widgets\Widget;
use Illuminate\Support\Collection;

class ActiveMessages extends Widget
{
    public Collection $messages;

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 10;

    public function __construct()
    {
        $this->messages = Message::activeMessages();
    }

    protected static string $view = 'filament.resources.message-resource.widgets.active-messages';
}
