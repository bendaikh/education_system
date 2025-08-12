<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, user-scalable=no">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="default">
        <meta name="theme-color" content="#ffffff">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Mobile Optimizations -->
        <style>
            /* Prevent horizontal scroll on mobile */
            html, body { 
                overflow-x: hidden; 
                -webkit-overflow-scrolling: touch;
            }
            
            /* Better touch targets for mobile */
            @media (max-width: 640px) {
                button, a, input, select, textarea {
                    min-height: 44px;
                    min-width: 44px;
                }
            }
            
            /* Prevent zoom on input focus for iOS */
            @media screen and (-webkit-min-device-pixel-ratio:0) {
                select, textarea, input[type="text"], input[type="password"], 
                input[type="datetime"], input[type="datetime-local"], 
                input[type="date"], input[type="month"], input[type="time"], 
                input[type="week"], input[type="number"], input[type="email"], 
                input[type="url"], input[type="search"], input[type="tel"], 
                input[type="color"] {
                    font-size: 16px;
                }
            }
            
            /* Custom sidebar animations and effects */
            .sidebar-gradient {
                background: linear-gradient(180deg, 
                    #0f172a 0%, 
                    #1e293b 25%, 
                    #334155 50%, 
                    #1e293b 75%, 
                    #0f172a 100%);
                background-size: 100% 200%;
                animation: gradientShift 10s ease infinite;
            }
            
            @keyframes gradientShift {
                0%, 100% { background-position: 0% 0%; }
                50% { background-position: 0% 100%; }
            }
            
            .nav-item-glow {
                position: relative;
                overflow: hidden;
            }
            
            .nav-item-glow::before {
                content: '';
                position: absolute;
                inset: 0;
                border-radius: 0.75rem;
                padding: 1px;
                background: linear-gradient(45deg, transparent, rgba(59, 130, 246, 0.3), transparent);
                mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
                mask-composite: xor;
                opacity: 0;
                transition: opacity 0.3s ease;
            }
            
            .nav-item-glow:hover::before {
                opacity: 1;
            }
        </style>

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
