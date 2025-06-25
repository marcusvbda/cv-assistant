<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\JobDescriptionAnalysisResource;
use App\Models\JobDescriptionAnalysis;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class RecentJobsAnalysesWidget extends BaseWidget
{
    protected static ?int $sort = 3;
    protected int|string|array $columnSpan = 'full';

    protected function getTableQuery(): Builder
    {
        return JobDescriptionAnalysis::query()->latest()->limit(5);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns(JobDescriptionAnalysisResource::getResourceTableCols());
    }
}
