<?php

namespace App\Providers\Filament;

use Caresome\FilamentAuthDesigner\AuthDesignerPlugin;
use Caresome\FilamentAuthDesigner\Enums\AuthLayout;
use Caresome\FilamentAuthDesigner\Enums\MediaDirection;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Althinect\FilamentSpatieRolesPermissions\FilamentSpatieRolesPermissionsPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id("admin")
            ->path("admin")
            ->login()
            ->brandName("Српски Вез")
            //            ->brandLogo("/images/logo.png")
            //            ->brandLogoHeight("20rem")
            ->sidebarCollapsibleOnDesktop(true)
            ->favicon("images/logo.png")
            ->colors([
                "primary" => Color::Amber,
            ])
            ->discoverResources(
                in: app_path("Filament/Resources"),
                for: "App\Filament\\Resources",
            )
            ->discoverPages(
                in: app_path("Filament/Pages"),
                for: "App\Filament\Pages",
            )
            ->pages([Dashboard::class])
            ->discoverWidgets(
                in: app_path("Filament/Widgets"),
                for: "App\Filament\Widgets",
            )
            ->widgets([AccountWidget::class, FilamentInfoWidget::class])
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
            ->plugins([
                FilamentSpatieRolesPermissionsPlugin::make(),
                AuthDesignerPlugin::make()->login(
                    layout: AuthLayout::Panel,
                    media: asset("images/login-image.jpeg"),
                    direction: MediaDirection::Left,
                ),
            ])
            ->authMiddleware([Authenticate::class]);
    }
}
