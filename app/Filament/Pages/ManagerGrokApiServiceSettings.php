<?php

namespace App\Filament\Pages;

use App\Settings\GrokApiServiceSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class ManagerGrokApiServiceSettings extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?int $navigationSort = 99;

    protected static string $settings = GrokApiServiceSettings::class;

    public static function getNavigationGroup(): ?string
    {
        return __('Grok API Service Settings');
    }

    public function getTitle(): string
    {
        return self::getNavigationLabel();
    }

    public static function getNavigationLabel(): string
    {
        return __('Grok API Service Settings');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('settings')->tabs([
                    Forms\Components\Tabs\Tab::make(__('Grok API Service'))->schema([
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
}
