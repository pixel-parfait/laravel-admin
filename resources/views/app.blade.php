<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" scroll-region>

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png"
        media="(prefers-color-scheme:no-preference)">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png" media="(prefers-color-scheme:light)">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16-dark.png"
        media="(prefers-color-scheme:dark)">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png"
        media="(prefers-color-scheme:no-preference)">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png" media="(prefers-color-scheme:light)">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32-dark.png"
        media="(prefers-color-scheme:dark)">
    <link rel="manifest" href="/site.webmanifest" crossorigin="use-credentials">
    <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">

    <meta name="msapplication-TileColor" content="#0065bd">
    <meta name="theme-color" content="#ffffff">
    <meta name="apple-mobile-web-app-title" content="La Criée">
    <meta name="application-name" content="La Criée">
    <meta name="msapplication-TileColor" content="#0065bd">
    <meta name="theme-color" content="#ffffff">

    @routes
    @inertiaHead

    @vite(['resources/js/app.js'], 'build')
</head>

<body>
    @inertia

    @env('staging')
    <div class="fixed left-0 top-0 z-60 rounded-br-md bg-red px-4 py-2 font-bold text-white">Staging</div>
    @endenv

    <div id="modals"></div>
</body>

</html>
