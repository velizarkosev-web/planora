<script setup lang="ts">
// `appName` is passed from the route in routes/web.php via Inertia::render.
defineProps<{
    appName: string;
}>();

const stack = ['Laravel 12', 'Inertia', 'Vue 3', 'TypeScript', 'Tailwind CSS'];
</script>

<template>
    <main class="flex min-h-screen items-center justify-center bg-slate-900 p-6 text-slate-100">
        <div class="w-full max-w-md rounded-2xl bg-slate-800 p-8 shadow-xl ring-1 ring-white/10">
            <p class="text-sm font-medium uppercase tracking-widest text-indigo-400">Welcome to</p>
            <h1 class="mt-1 text-4xl font-bold">{{ appName }}</h1>
            <p class="mt-4 text-slate-300">
                Your storefront foundation is live — rendered as a Vue component through Inertia,
                typed with TypeScript, styled with Tailwind.
            </p>
            <div class="mt-6 flex flex-wrap gap-2 text-xs">
                <span
                    v-for="item in stack"
                    :key="item"
                    class="rounded-full bg-slate-700 px-3 py-1"
                >{{ item }}</span>
            </div>
        </div>
    </main>
</template>
