<?php

namespace App\Filament\Pages;

use App\Settings\GeneralSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class ManagerGeneral extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $settings = GeneralSettings::class;

    public function getTitle(): string
    {
        return __('Settings');
    }

    public static function getNavigationLabel(): string
    {
        return __('Settings');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('User Details')->tabs([
                    Forms\Components\Tabs\Tab::make(__('AI Integration'))->schema([
                        Forms\Components\Select::make('ai_provider')
                            ->label(__("Provider"))
                            ->options([
                                'groq' => 'GROQ',
                                'openai' => 'OpenAI',
                            ])
                            ->default('groq'),
                        Forms\Components\TextInput::make('ai_key')
                            ->label(__('API Key'))
                            ->password()
                            ->revealable()
                    ])->columns(2),
                ])
            ])->columns(1);
    }
}
