<?php

namespace App\Filament\Resources\JobDescriptionAnalysisResource\Pages;

use App\Filament\Resources\JobDescriptionAnalysisResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListJobDescriptionAnalyses extends ListRecords
{
    protected static string $resource = JobDescriptionAnalysisResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
