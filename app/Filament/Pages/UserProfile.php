<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms;
use Auth;
use Filament\Notifications\Notification;

class UserProfile extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'User Profile';
    protected static string $view = 'filament.pages.user-profile';
    protected static ?string $title = 'Edit User Profile';

    public $name;
    public $email;

    public function getBreadcrumbs(): array
    {
        return [
            url('/user-profile') => 'User Profile',
            'edit' => 'Edit',
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
