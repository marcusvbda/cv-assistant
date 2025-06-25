<?php

namespace App\Filament\Resources\JobDescriptionAnalysisResource\Pages;

use App\Filament\Resources\JobDescriptionAnalysisResource;
use App\Filament\Widgets\AlertNoAiIntegration;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListJobDescriptionAnalyses extends ListRecords
{
    protected static string $resource = JobDescriptionAnalysisResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            AlertNoAiIntegration::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
