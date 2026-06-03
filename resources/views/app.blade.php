<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- The page <title> is managed by Inertia (see app.ts `title` callback). --}}
    <title inertia>{{ config('app.name', 'Planora') }}</title>

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
