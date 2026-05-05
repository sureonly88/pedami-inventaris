<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Support\Facades\FilamentView;
use Illuminate\Support\Facades\Blade;


class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->renderHook(
                'panels::head.end',
                function (): string {
                    return Blade::render(<<<'BLADE'
            <style>
                /* Global Sidebar Background - SOFT PINK */
                aside.fi-sidebar { background-color: #fdf2f8 !important; border-right: 1px solid #fce7f3 !important; }
                .fi-sidebar-nav { background-color: transparent !important; }
                
                /* Menu Items Normal - DEEP PINK TEXT */
                .fi-sidebar-item a, .fi-sidebar-item button, 
                .fi-sidebar-item-label, .fi-sidebar-item-icon { 
                    color: #9d174d !important; 
                    transition: all 0.2s ease !important;
                }

                /* Hover State */
                .fi-sidebar-item:hover a, .fi-sidebar-item:hover button { 
                    background-color: #fce7f3 !important; 
                    border-radius: 0.75rem !important; 
                }

                /* Active/Selected State - BRIGHT PINK */
                .fi-sidebar-item-active, 
                [class*="fi-sidebar-item-active"],
                .fi-sidebar-item[class*="active"] a { 
                    background-color: #fbcfe8 !important; 
                    color: #be185d !important;
                    border-radius: 0.75rem !important;
                    box-shadow: 0 4px 6px -1px rgba(244, 114, 182, 0.2) !important;
                }

                .fi-sidebar-item-active .fi-sidebar-item-label, 
                .fi-sidebar-item-active .fi-sidebar-item-icon { 
                    color: #be185d !important; 
                    font-weight: 800 !important; 
                }

                /* Brand Area - Khusus Sidebar - Lebih Kecil & Rapi */
                .fi-sidebar-header { 
                    background: linear-gradient(135deg, #fbcfe8 0%, #fdf2f8 100%) !important;
                    margin: 0.5rem !important;
                    padding: 0.5rem !important; 
                    border-radius: 1.25rem !important;
                    border: 2px solid #fce7f3 !important;
                    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05) !important;
                    display: flex !important;
                    align-items: center !important;
                    justify-content: center !important;
                    min-height: 70px !important; /* Menjaga agar tidak terpotong */
                }

                .fi-sidebar-header img { 
                    height: 40px !important; /* Logo lebih kecil di sidebar saja */
                    width: auto !important;
                }

                .fi-sidebar-header span { 
                    color: #be185d !important; 
                    font-weight: 800 !important; 
                    font-size: 1.15rem !important; /* Teks lebih pas untuk sidebar */
                    letter-spacing: 0px !important;
                }

                /* Group Labels */
                .fi-sidebar-group-label { 
                    color: #f472b6 !important; 
                    font-weight: 700 !important; 
                    text-transform: uppercase !important;
                    letter-spacing: 0.1em !important;
                    font-size: 0.75rem !important;
                }

                /* Top Bar - SOFT PINK */
                header.fi-topbar, 
                .fi-topbar-nav, 
                .fi-topbar > nav { 
                    background: #fdf2f8 !important; 
                    background-color: #fdf2f8 !important;
                    border-left: 2px solid #fbcfe8 !important;
                    border-bottom: 2px solid #fce7f3 !important;
                    box-shadow: none !important;
                }

                /* Breadcrumbs - SOFT PINK CONTRAST */
                .fi-breadcrumbs-item-label, 
                .fi-breadcrumbs-item-label a {
                    color: #9d174d !important; 
                    font-weight: 700 !important;
                }

                .fi-breadcrumbs-item-separator {
                    color: #f472b6 !important;
                }

                .fi-breadcrumbs-item-label:hover a {
                    color: #be185d !important;
                }

                /* LOGIN PAGE STYLES - Custom sakura background with petals animation and no-scroll */
                body.fi-body.fi-simple-layout, 
                .fi-simple-layout,
                .fi-simple-layout main { 
                    background-image: url('/images/sakura_bg.png') !important;
                    background-size: cover !important;
                    background-position: center !important;
                    background-repeat: no-repeat !important;
                    background-attachment: fixed !important;
                    min-height: 100vh !important;
                    max-height: 100vh !important;
                    overflow: hidden !important;
                }

                .sakura-petals-container {
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    pointer-events: none;
                    z-index: 1;
                    overflow: hidden;
                }

                .sakura-petal {
                    position: absolute;
                    background: linear-gradient(to right, #ffb7c5, #ff9a9e);
                    border-radius: 150% 0 150% 0;
                    opacity: 0.8;
                    box-shadow: 0 0 10px rgba(255, 182, 193, 0.4);
                    animation: fall-sakura linear infinite;
                }

                @keyframes fall-sakura {
                    0% {
                        transform: translate(0, -10%) rotate(0deg);
                        opacity: 0;
                    }
                    10% {
                        opacity: 0.8;
                    }
                    90% {
                        opacity: 0.8;
                    }
                    100% {
                        transform: translate(100vw, 110vh) rotate(720deg);
                        opacity: 0;
                    }
                }

                .fi-simple-main-ctn { 
                    background: transparent !important;
                    backdrop-filter: none !important;
                    -webkit-backdrop-filter: none !important;
                    border: none !important;
                    box-shadow: none !important;
                    padding: 2rem 1rem !important;
                    transition: all 0.4s ease !important;
                    max-width: 520px !important;
                    width: 90% !important;
                    margin: auto !important;
                }

                .fi-simple-main-ctn:hover {
                    background: transparent !important;
                    box-shadow: none !important;
                }

                /* Container alignment and spacing */
                .fi-simple-layout main {
                    display: flex !important;
                    align-items: center !important;
                    justify-content: center !important;
                    padding: 2rem !important;
                }

                .fi-simple-main {
                    max-width: 480px !important;
                    width: 100% !important;
                    z-index: 10 !important;
                }

                /* Enhanced Labels & Typography */
                .fi-simple-main label, 
                .fi-simple-main .fi-fo-field-wrp-label span,
                .fi-simple-main .fi-checkbox-label {
                    color: #9d174d !important;
                    font-weight: 800 !important;
                    font-size: 0.95rem !important;
                    letter-spacing: 0.2px !important;
                    margin-bottom: 0.5rem !important;
                    display: block !important;
                }

                /* Inputs with soft pink borders */
                .fi-simple-main input {
                    border-radius: 1rem !important;
                    border: 1.5px solid #fce7f3 !important;
                    transition: all 0.2s ease !important;
                    padding: 0.75rem 1rem !important;
                }

                .fi-simple-main input:focus {
                    border-color: #f472b6 !important;
                    box-shadow: 0 0 0 4px rgba(244, 114, 182, 0.1) !important;
                }

                /* Menampilkan logo di halaman login - Premium Spacing */
                .fi-simple-header {
                    display: flex !important;
                    flex-direction: column !important;
                    align-items: center !important;
                    justify-content: center !important;
                    margin-bottom: 2rem !important;
                    padding-bottom: 0 !important;
                    z-index: 10 !important;
                }

                .fi-simple-header-heading {
                    color: #be185d !important;
                    font-weight: 950 !important;
                    margin-top: 1rem !important; 
                    letter-spacing: -1.5px !important;
                    text-align: center !important;
                    font-size: 2.5rem !important;
                    text-shadow: 0 2px 4px rgba(255, 255, 255, 0.8) !important;
                    line-height: 1.1 !important;
                }

                /* Login Button - Premium Gradient */
                .fi-btn[type="submit"] {
                    background: linear-gradient(135deg, #be185d 0%, #9d174d 100%) !important;
                    color: #ffffff !important;
                    box-shadow: 0 6px 15px -3px rgba(190, 24, 93, 0.4) !important;
                    border-radius: 1.25rem !important;
                    font-weight: 700 !important;
                    padding: 0.75rem 2rem !important;
                    text-transform: uppercase !important;
                    letter-spacing: 1px !important;
                    transition: all 0.3s ease !important;
                    border: none !important;
                }

                .fi-btn[type="submit"]:hover {
                    transform: translateY(-2px) !important;
                    box-shadow: 0 10px 20px -5px rgba(190, 24, 93, 0.5) !important;
                    filter: brightness(1.1) !important;
                }

            </style>

            <script>
            document.addEventListener('DOMContentLoaded', function() {
                const layout = document.querySelector('.fi-simple-layout');
                if (!layout) return;

                const container = document.createElement('div');
                container.className = 'sakura-petals-container';
                document.body.prepend(container);

                const petalCount = 30;
                for (let i = 0; i < petalCount; i++) {
                    createPetal(container);
                }

                function createPetal(target) {
                    const petal = document.createElement('div');
                    petal.className = 'sakura-petal';
                    
                    const size = Math.random() * 10 + 10; // 10-20px
                    const startPos = Math.random() * 100; // 0-100% left
                    const duration = Math.random() * 10 + 10; // 10-20s
                    const delay = Math.random() * 15; // 0-15s

                    petal.style.width = size + 'px';
                    petal.style.height = (size * 0.8) + 'px';
                    petal.style.left = startPos + 'vw';
                    petal.style.top = '-20px';
                    petal.style.animationDuration = duration + 's';
                    petal.style.animationDelay = -delay + 's'; // Negative delay for pre-start
                    
                    target.appendChild(petal);
                    
                    petal.addEventListener('animationiteration', () => {
                        petal.style.left = Math.random() * 100 + 'vw';
                        petal.style.animationDuration = (Math.random() * 10 + 10) + 's';
                    });
                }
            });
            </script>
BLADE);
                },
            )
            ->renderHook(
                'panels::user-menu.before',
                fn (): string => Blade::render('
                    <!-- 1. Date & Time (Stays on Right) -->
                    <div class="hidden lg:flex items-center gap-8 mr-12 border-l border-pink-100 pl-8 transition-all">
                        <div class="flex flex-col items-end gap-2 whitespace-nowrap text-right">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.25em] leading-none mb-0.5">
                                {{ now()->locale(\'id\')->translatedFormat(\'l, d F Y\') }}
                            </span>
                            <span id="live-clock" class="text-2xl font-black tracking-[-0.05em] text-[#be185d] font-mono leading-none">
                                {{ now()->format(\'H:i:s\') }}
                            </span>
                        </div>
                    </div>

                    <script>
                        function updateClock() {
                            const now = new Date();
                            const hours = String(now.getHours()).padStart(2, "0");
                            const minutes = String(now.getMinutes()).padStart(2, "0");
                            const seconds = String(now.getSeconds()).padStart(2, "0");
                            const clockEl = document.getElementById("live-clock");
                            if (clockEl) {
                                clockEl.textContent = `${hours}:${minutes}:${seconds}`;
                            }
                        }
                        setInterval(updateClock, 1000);
                    </script>
                '),
            )
            ->colors([
                'primary' => Color::Sky,
                'gray' => Color::Slate,
            ])
            ->font('Inter')
            ->brandLogo(new \Illuminate\Support\HtmlString('
                <div class="flex items-center justify-center gap-4 whitespace-nowrap w-full">
                    <img src="/images/logo.png" alt="Logo" style="height: 80px; width: auto;">
                    <span class="text-3xl font-black tracking-tighter" style="color: #be185d;">PEDAMI INVENTARIS</span>
                </div>
            '))
            ->brandLogoHeight('80px')
            ->spa()
            ->sidebarCollapsibleOnDesktop()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                \App\Filament\Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                \App\Filament\Widgets\LegendWidget::class,
                //Widgets\FilamentInfoWidget::class,
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
