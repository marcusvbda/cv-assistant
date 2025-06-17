<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class DashboardMetrics extends StatsOverviewWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $user = Auth::user();

        return [
            Stat::make('Addresses', $user->addresses()->count())
                ->description('Total registered addresses')
                ->icon('heroicon-o-map-pin')
                ->color('primary'),

            Stat::make('Contacts', $user->addresses()->count())
                ->description('Total registered contacts')
                ->icon('heroicon-o-phone')
                ->color('primary'),

            Stat::make('Websites', $user->links()->count())
                ->description('Total registered websites')
                ->icon('heroicon-o-globe-alt')
                ->color('primary'),

            Stat::make('Skills', $user->skills()->count())
                ->description('Total registered skills')
                ->icon('heroicon-o-sparkles')
                ->color('primary'),

            Stat::make('Courses', $user->courses()->count())
                ->description('Total registered courses')
                ->icon('heroicon-o-academic-cap')
                ->color('primary'),

            Stat::make('Experiences', $user->experiences()->count())
                ->description('Total registered experiences')
                ->icon('heroicon-o-briefcase')
                ->color('primary'),

            Stat::make('Projects', $user->projects()->count())
                ->description('Total registered projects')
                ->icon('heroicon-o-code-bracket')
                ->color('primary'),

            Stat::make('Certificates', $user->certificates()->count())
                ->description('Total registered certificates')
                ->icon('heroicon-o-document-check')
                ->color('primary'),
        ];
    }
}
