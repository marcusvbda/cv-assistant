<?php

namespace App\Filament\Resources\IntegrationsResource\Pages;

use App\Filament\Resources\IntegrationsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListIntegrations extends ListRecords
{
    protected static string $resource = IntegrationsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
