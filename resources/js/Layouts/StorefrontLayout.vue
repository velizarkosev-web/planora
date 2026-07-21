<script setup lang="ts">
import { useLocale } from '../composables/useLocale';

// Shared locale ref — the toggle below flips it; every page reading useLocale() reacts.
const { locale } = useLocale();
</script>

<template>
    <div class="flex min-h-screen flex-col bg-[#fdfbf7] text-slate-800 antialiased">
        <!-- Header -->
        <header class="sticky top-0 z-20 border-b border-black/5 bg-[#fdfbf7]/80 backdrop-blur">
            <div class="mx-auto flex max-w-6xl items-center justify-between px-6 py-4">
                <!-- Logo -->
                <a href="/" class="text-2xl font-extrabold tracking-tight">Planora</a>

                <!-- Center nav -->
                <nav class="hidden gap-8 text-sm font-medium text-slate-600 sm:flex">
                    <a href="/" class="transition hover:text-slate-900">{{ locale === 'bg' ? 'Планери' : 'Planners' }}</a>
                    <a href="/" class="transition hover:text-slate-900">{{ locale === 'bg' ? 'Полезно' : 'Useful' }}</a>
                </nav>

                <!-- Language toggle + cart placeholder -->
                <div class="flex items-center gap-4">
                    <div class="flex items-center rounded-full bg-slate-100 p-1 text-sm font-semibold">
                        <button
                            class="rounded-full px-3 py-1 transition"
                            :class="locale === 'bg' ? 'bg-white text-slate-900 shadow' : 'text-slate-500'"
                            @click="locale = 'bg'"
                        >БГ</button>
                        <button
                            class="rounded-full px-3 py-1 transition"
                            :class="locale === 'en' ? 'bg-white text-slate-900 shadow' : 'text-slate-500'"
                            @click="locale = 'en'"
                        >EN</button>
                    </div>
                    <!-- Cart is Epic 5; placeholder for now -->
                    <span class="cursor-not-allowed text-xl opacity-70" :title="locale === 'bg' ? 'Кошница (скоро)' : 'Cart (soon)'">🛒</span>
                </div>
            </div>
        </header>

        <!-- Page content goes here -->
        <main class="flex-1">
            <slot />
        </main>

        <!-- Footer -->
        <footer class="mt-16 border-t border-black/5 py-10 text-center">
            <p class="text-lg font-extrabold tracking-tight">Planora</p>
            <p class="mt-2 text-sm text-slate-400">
                {{ locale === 'bg' ? 'Изработено с грижа' : 'Crafted with care' }}
            </p>
        </footer>
    </div>
</template>
