<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): HtmlString => new HtmlString(<<<'HTML'
                    <style>
                        @font-face {
                            font-family: 'Material Symbols Outlined';
                            font-display: swap;
                            font-style: normal;
                            font-weight: 400;
                            src: url('/fonts/material-symbols/material-symbols-outlined-400.ttf') format('truetype');
                        }

                        .lumina-filament-icon-option {
                            align-items: center;
                            display: inline-flex;
                            gap: 0.625rem;
                            min-width: 0;
                        }

                        .lumina-filament-icon-preview {
                            align-items: center;
                            background: rgb(239 246 255);
                            border: 1px solid rgb(191 219 254);
                            border-radius: 0.5rem;
                            color: rgb(37 99 235);
                            display: inline-flex;
                            flex: none;
                            font-family: 'Material Symbols Outlined';
                            font-size: 1.125rem;
                            font-style: normal;
                            font-variation-settings: 'FILL' 0, 'wght' 500, 'GRAD' 0, 'opsz' 24;
                            height: 2rem;
                            justify-content: center;
                            line-height: 1;
                            width: 2rem;
                        }

                        .lumina-filament-icon-name {
                            color: rgb(100 116 139);
                            font-size: 0.75rem;
                        }
                    </style>
                HTML),
            )
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
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
            ]);
    }
}
