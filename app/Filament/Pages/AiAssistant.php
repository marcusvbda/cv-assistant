<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms;
use Auth;

class AiAssistant extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-command-line';
    protected static string $view = 'filament.pages.ai-assistant';

    public function getTitle(): string
    {
        return static::getNavigationLabel();
    }

    public static function getNavigationLabel(): string
    {
        return __('AI Assistant');
    }

    public static function canAccess(): bool
    {
        return Auth::user()->hasAiIntegration();
    }
}
