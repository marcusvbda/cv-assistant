<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms;
use Auth;

class AiAssistant extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-command-line';
    protected static ?string $navigationLabel = 'AI Assistant';
    protected static string $view = 'filament.pages.ai-assistant';
    protected static ?string $title = 'AI Assistant';

    public static function canAccess(): bool
    {
        // return true;
        return Auth::user()->hasAiIntegration();
    }
}
