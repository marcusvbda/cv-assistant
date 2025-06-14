<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms;
use Auth;
// use Filament\Notifications\Notification;

class Resume extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
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
