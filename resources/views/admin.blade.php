<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
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
    <link rel="manifest" href="/site.webmanifest">
    <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">

    <meta name="msapplication-TileColor" content="#0065bd">
    <meta name="theme-color" content="#ffffff">
    <meta name="apple-mobile-web-app-title" content="La Criée">
    <meta name="application-name" content="La Criée">
    <meta name="msapplication-TileColor" content="#0065bd">
    <meta name="theme-color" content="#ffffff">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">

    @routes('all')
    @inertiaHead

    {{ Vite::useHotFile('hot.admin')->useBuildDirectory('build-admin')->withEntryPoints(['resources/sass/admin.scss', 'resources/js/admin.js']) }}
</head>

<body>
    @inertia

    <div id="modals"></div>
</body>

</html>
