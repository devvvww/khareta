    <!DOCTYPE html>
    <html lang="ar" dir="rtl">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>@yield('title', 'مسار المواد الدراسية')</title>

        {{-- NOTE: for production, replace the Tailwind CDN build with a compiled
            Tailwind build (npm run build) wired through Vite. The CDN script is
            kept here only to mirror the original static demo 1:1. --}}
        {{-- <script src="https://cdn.tailwindcss.com"></script> --}}


        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('styles')
        <script src="https://telegram.org/js/telegram-web-app.js"></script>
    </head>

    <body class="bg-slate-50 text-slate-900 min-h-screen"
        data-tg-root="@yield('tg-root', 'false')"
        data-tg-fallback="@yield('tg-fallback', '/')">

        @yield('content')

        @stack('scripts')

        <script>
            if (window.Telegram && window.Telegram.WebApp) {
                const tg = window.Telegram.WebApp;
                tg.ready();
                tg.expand();
                tg.disableVerticalSwipes();

                const isRootPage = document.body.dataset.tgRoot === 'true';
                const fallbackUrl = document.body.dataset.tgFallback || '/';

                if (isRootPage) {
                    tg.BackButton.hide();
                } else {
                    tg.BackButton.show();
                }

                tg.BackButton.onClick(() => {
                    if (window.history.length > 1) {
                        window.history.back();
                    } else {
                        window.location.href = fallbackUrl;
                    }
                });
            }
        </script>
    </body>

    </html>
