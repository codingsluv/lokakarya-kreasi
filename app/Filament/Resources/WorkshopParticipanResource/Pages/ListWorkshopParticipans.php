<?php

namespace App\Filament\Resources\WorkshopParticipanResource\Pages;

use App\Filament\Resources\WorkshopParticipanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWorkshopParticipans extends ListRecords
{
    protected static string $resource = WorkshopParticipanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
