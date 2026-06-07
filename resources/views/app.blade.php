<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- The page <title> is managed by Inertia (see app.ts `title` callback). --}}
    <title inertia>{{ config('app.name', 'Planora') }}</title>

    {{-- Elegant flowing script font for the Planora wordmark. --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Cormorant+Garamond:ital,wght@0,400;1,500&display=swap" rel="stylesheet">

    {{-- Vite injects the compiled CSS + JS (hashed for cache-busting). --}}
    @vite(['resources/css/app.css', 'resources/js/app.ts'])

    {{-- Inertia injects per-page <head> tags here. --}}
    @inertiaHead
</head>
<body class="font-sans antialiased">
    {{-- Inertia mounts the Vue app into this element and injects initial page data. --}}
    @inertia
</body>
</html>
