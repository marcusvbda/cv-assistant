<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Auth;

class CoverLetter extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Cover Letter';
    protected static string $view = 'filament.pages.cover-letter';
    protected static ?string $title = 'Your cover letter';

    public $user;

    public function getBreadcrumbs(): array
    {
        return [
            url('/resume') => 'Cover Letter',
            'generate' => 'Your cover letter',
        ];
    }

    public function mount(): void
    {
        $this->user = Auth::user();
    }
}
