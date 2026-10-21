<?php

namespace App\Filament\Resources\SubscriptionResource\Pages;

use App\Filament\Resources\SubscriptionResource;
use App\Models\Subscription;
use Filament\Actions;
use Filament\Actions\MountableAction;
use Filament\Resources\Pages\EditRecord;

class EditSubscription extends EditRecord
{
    protected static string $resource = SubscriptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->before(fn (Subscription $record, MountableAction $action) => SubscriptionResource::cancelDeletionWhenUsed($record, $action)),
        ];
    }
}
