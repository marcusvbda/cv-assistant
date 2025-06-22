<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Auth;

class Resume extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document';
    protected static ?string $navigationLabel = 'Resume';
    protected static string $view = 'filament.pages.resume';
    protected static ?string $title = 'Your resume';

    public $user;

    public function getBreadcrumbs(): array
    {
        return [
            url('/resume') => 'Resume',
            'generate' => 'Your resume',
        ];
    }

    public function mount(): void
    {
        $this->user = Auth::user();
    }
}
