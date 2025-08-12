<?php

namespace App\Filament\Pages;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Mvbassalobre\GroqApiService\Settings\GroqApiServiceSettings;

class ManagerGroqApiServiceSettings extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?int $navigationSort = 99;

    protected static string $settings = GroqApiServiceSettings::class;

    public static function getNavigationGroup(): ?string
    {
        return __('Groq API Service Settings');
    }

    public function getTitle(): string
    {
        return self::getNavigationLabel();
    }

    public static function getNavigationLabel(): string
    {
        return __('Groq API Service Settings');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('settings')->tabs([
                    Forms\Components\Tabs\Tab::make(__('Groq API Service'))->schema([
                        Forms\Components\RichEditor::make('settings.initial_instructions')
                            ->label(__("Initial Instructions"))
                            ->disableToolbarButtons(['attachFiles'])
                            ->required(),
                        Forms\Components\RichEditor::make('settings.absolute_rules')
                            ->label(__("Absolute Rules"))
                            ->disableToolbarButtons(['attachFiles'])
                            ->required(),
                        Forms\Components\RichEditor::make('settings.expected_response_type')
                            ->label(__("Expected Response Type"))
                            ->disableToolbarButtons(['attachFiles'])
                            ->required(),
                        Forms\Components\RichEditor::make('settings.main_context')
                            ->label(__("Main Context"))
                            ->disableToolbarButtons(['attachFiles'])
                            ->required(),
                    ]),
                ])
            ])->columns(1);
    }

    public static function canAccess(): bool
    {
        return true;
    }
}
