<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Auth;
use Filament\Widgets\Concerns\CanPoll;

class AlertNoAiIntegration extends Widget
{
    use CanPoll;

    protected static string $view = 'filament.widgets.ai-not-configured';
    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        $user = Auth::user();
        return !$user->hasAiIntegration();
    }
}
