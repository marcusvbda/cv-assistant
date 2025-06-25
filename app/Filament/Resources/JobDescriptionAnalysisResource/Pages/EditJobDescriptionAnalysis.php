<?php

namespace App\Filament\Resources\JobDescriptionAnalysisResource\Pages;

use App\Filament\Resources\JobDescriptionAnalysisResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditJobDescriptionAnalysis extends EditRecord
{
    protected static string $resource = JobDescriptionAnalysisResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
