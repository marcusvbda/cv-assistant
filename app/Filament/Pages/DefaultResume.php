<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Auth;

class DefaultResume extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document';
    protected static ?string $navigationLabel = 'Default resume (CV)';
    protected static string $view = 'filament.pages.resume';
    protected static ?string $title = 'Your default resume (CV)';

    public $user;

    public function getBreadcrumbs(): array
    {
        return [
            url('/resume') => 'Default resume (CV)',
            'generate' => 'Your default resume (CV)',
        ];
    }

    public function mount(): void
    {
        $this->user = Auth::user();
    }
}
