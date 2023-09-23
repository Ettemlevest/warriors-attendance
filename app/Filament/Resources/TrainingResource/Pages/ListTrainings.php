<?php

namespace App\Filament\Resources\TrainingResource\Pages;

use App\Filament\Resources\TrainingResource;
use App\Models\Training;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListTrainings extends ListRecords
{
    protected static string $resource = TrainingResource::class;

    protected function getTableQuery(): Builder
    {
        return Training::query()
            ->withCount('attendees');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
