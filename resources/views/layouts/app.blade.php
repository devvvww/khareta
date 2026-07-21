<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'مسار المواد الدراسية')</title>

    {{-- NOTE: for production, replace the Tailwind CDN build with a compiled
         Tailwind build (npm run build) wired through Vite. The CDN script is
         kept here only to mirror the original static demo 1:1. --}}
    <script src="https://cdn.tailwindcss.com"></script>
    

    {{-- Tajawal is now self-hosted (see @font-face rules in course-flow.css
         and public/fonts/tajawal/) instead of being fetched from
         fonts.googleapis.com, so the page has no external font request. --}}
    <link rel="stylesheet" href="{{ asset('css/course-flow.css') }}">

    @stack('styles')
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen">

    @yield('content')

    @stack('scripts')
</body>
</html>