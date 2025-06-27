<?php

namespace App\Filament\Resources\JobDescriptionAnalysisResource\Pages;

use App\Filament\Resources\JobDescriptionAnalysisResource;
use App\Filament\Widgets\AlertNoAiIntegrationWidget;
use App\Filament\Widgets\LinkedinAnalysisWidget;
use App\Filament\Widgets\ProfileAnalysisWidget;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListJobDescriptionAnalyses extends ListRecords
{
    protected static string $resource = JobDescriptionAnalysisResource::class;

    protected function getFooterWidgets(): array
    {
        return [
            AlertNoAiIntegrationWidget::class,
            LinkedinAnalysisWidget::class,
            ProfileAnalysisWidget::class
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
