<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\LinkedinAnalysisWidget;
use App\Filament\Widgets\ProfileAnalysisWidget;
use App\Filament\Widgets\RecentJobsAnalysesWidget;
use App\Http\Middleware\mustVerifyMiddleware;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Pages\Auth\Register;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('/')
            ->login()
            ->registration(Register::class)
            ->emailVerification()
            ->passwordReset()
            ->profile()
            ->spa()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->theme(asset('css/filament/admin/theme.css'))
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->renderHook(
                'panels::footer',
                fn(): string => <<<HTML
                    <a href="https://www.buymeacoffee.com/mvbassalobre" target="_blank" class="p-4 fixed" style="bottom: 0; right: 0;">
                        <img src="https://cdn.buymeacoffee.com/buttons/v2/default-yellow.png" alt="Buy Me A Coffee" style="height: 40px; width: auto;" />
                    </a>
                HTML
            )
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                LinkedinAnalysisWidget::class,
                ProfileAnalysisWidget::class,
                RecentJobsAnalysesWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                mustVerifyMiddleware::class,
            ]);
    }
}
