<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms;
use Auth;
use Filament\Notifications\Notification;

class Resume extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Resume';
    protected static string $view = 'filament.pages.resume';
    protected static ?string $title = 'Generate resume';

    public $name;
    public $email;

    public function getBreadcrumbs(): array
    {
        return [
            url('/resume') => 'Resume',
            'generate' => 'Generate',
        ];
    }

    public function mount(): void
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('name')->required()->label('Nome'),
            Forms\Components\TextInput::make('email')->email()->disabled()->label('E-mail'),
        ];
    }

    public function submit()
    {
        $data = $this->form->getState();

        $user = Auth::user();
        $user->update($data);

        Notification::make()
            ->title('Saved successfully')
            ->success()
            ->send();
    }
}
