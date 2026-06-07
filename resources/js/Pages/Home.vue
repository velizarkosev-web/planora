<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

type Locale = 'en' | 'bg';

interface Translated {
    en: string;
    bg: string;
}

interface Category {
    slug: string;
    name: Translated;
    productCount: number;
}

interface Product {
    slug: string;
    name: Translated;
    category: Translated;
    priceFrom: number; // cents
    onSale: boolean;
    variantCount: number;
    specs: Record<string, string | number> | null;
}

defineProps<{
    categories: Category[];
    products: Product[];
}>();

// Live language switch — shows off the bilingual catalog.
const locale = ref<Locale>('en');

// Small UI dictionary so the whole page (not just product names) translates.
const ui = {
    en: {
        nav: 'Plan beautifully',
        heroTitle: 'Planning, made beautiful.',
        heroSub: 'Thoughtfully designed planners, notebooks and stickers to make every day feel intentional.',
        shop: 'Shop the collection',
        ourCategories: 'Browse by category',
        featured: 'Featured products',
        from: 'from',
        sale: 'SALE',
        options: (n: number) => (n > 1 ? `${n} options` : '1 option'),
        items: (n: number) => (n === 1 ? '1 product' : `${n} products`),
        footer: 'Crafted with care · Available in Български & English',
    },
    bg: {
        nav: 'Планирай красиво',
        heroTitle: 'Планиране, превърнато в изкуство.',
        heroSub: 'Внимателно създадени тефтери, бележници и стикери, които правят всеки ден осмислен.',
        shop: 'Разгледай колекцията',
        ourCategories: 'Разгледай по категория',
        featured: 'Препоръчани продукти',
        from: 'от',
        sale: 'НАМАЛЕНИЕ',
        options: (n: number) => (n > 1 ? `${n} опции` : '1 опция'),
        items: (n: number) => (n === 1 ? '1 продукт' : `${n} продукта`),
        footer: 'Изработено с грижа · Налично на Български & English',
    },
} as const;

const t = computed(() => ui[locale.value]);

// A palette of soft, playful gradients for the product "covers".
const accents = [
    'from-indigo-400 to-violet-500',
    'from-rose-400 to-pink-500',
    'from-amber-300 to-orange-400',
    'from-teal-400 to-emerald-500',
    'from-sky-400 to-blue-500',
    'from-fuchsia-400 to-purple-500',
];

const emojiFor = (slug: string): string => {
    if (slug.includes('plan')) return '📓';
    if (slug.includes('sticker')) return '✨';
    return '✦';
};

const price = (cents: number): string => `€${(cents / 100).toFixed(2)}`;

const specsLine = (specs: Product['specs']): string =>
    specs ? Object.values(specs).join(' · ') : '';
</script>

<template>
    <Head title="Planora — Planning, made beautiful" />

    <div class="min-h-screen bg-[#fdfbf7] text-slate-800 antialiased">
        <!-- ✨ seasonal banner — a seasonal promo; remove after the big day ✨ -->
        <div class="relative overflow-hidden bg-gradient-to-r from-rose-500 via-pink-500 to-fuchsia-500 px-6 py-4 text-center text-white shadow-md">
            <p class="text-lg font-bold tracking-wide sm:text-2xl">🌹 Welcome to Planora! ♥</p>
            <p class="mt-1 text-sm text-rose-50 sm:text-base">
                You're a planner for every day — thoughtfully designed! 💕
            </p>
        </div>

        <!-- Nav -->
        <header class="sticky top-0 z-20 border-b border-black/5 bg-[#fdfbf7]/80 backdrop-blur">
            <div class="mx-auto flex max-w-6xl items-center justify-between px-6 py-4">
                <div class="flex items-baseline gap-2">
                    <span class="text-2xl font-extrabold tracking-tight">Planora</span>
                    <span class="hidden text-sm text-slate-400 sm:inline">· {{ t.nav }}</span>
                </div>
                <div class="flex items-center rounded-full bg-slate-100 p-1 text-sm font-semibold">
                    <button
                        class="rounded-full px-3 py-1 transition"
                        :class="locale === 'en' ? 'bg-white shadow text-slate-900' : 'text-slate-500'"
                        @click="locale = 'en'"
                    >EN</button>
                    <button
                        class="rounded-full px-3 py-1 transition"
                        :class="locale === 'bg' ? 'bg-white shadow text-slate-900' : 'text-slate-500'"
                        @click="locale = 'bg'"
                    >БГ</button>
                </div>
            </div>
        </header>

        <!-- Hero -->
        <section class="relative overflow-hidden">
            <div class="pointer-events-none absolute -left-24 -top-24 h-72 w-72 rounded-full bg-violet-300/40 blur-3xl"></div>
            <div class="pointer-events-none absolute -right-20 top-10 h-72 w-72 rounded-full bg-rose-300/40 blur-3xl"></div>
            <div class="relative mx-auto max-w-6xl px-6 py-20 text-center sm:py-28">
                <!-- Flower garland above -->
                <div class="select-none text-2xl sm:text-3xl">🌷&nbsp;🌸&nbsp;🌹&nbsp;🌼&nbsp;💐&nbsp;🌸&nbsp;🌷</div>

                <!-- Planora wordmark in flowing script, flowers either side -->
                <div class="mt-2 flex items-center justify-center gap-3 sm:gap-6">
                    <span class="-rotate-12 select-none text-4xl sm:text-6xl">🌹</span>
                    <h1
                        style="font-family: 'Great Vibes', cursive"
                        class="bg-gradient-to-r from-rose-500 via-fuchsia-500 to-violet-500 bg-clip-text pb-2 text-7xl leading-none text-transparent sm:text-9xl"
                    >Planora</h1>
                    <span class="rotate-12 select-none text-4xl sm:text-6xl">🌷</span>
                </div>

                <!-- Flower garland below -->
                <div class="mt-1 select-none text-2xl sm:text-3xl">🌸&nbsp;🌼&nbsp;🌹&nbsp;🌷&nbsp;🌺&nbsp;🌼&nbsp;🌸</div>

                <p
                    class="mx-auto mt-8 max-w-xl text-xl italic text-slate-600"
                    style="font-family: 'Cormorant Garamond', serif"
                >{{ t.heroSub }}</p>
                <a
                    href="#products"
                    class="mt-9 inline-block rounded-full bg-slate-900 px-8 py-3.5 text-sm font-semibold text-white shadow-lg shadow-slate-900/10 transition hover:-translate-y-0.5 hover:bg-slate-800"
                >{{ t.shop }}</a>
            </div>
        </section>

        <!-- Categories -->
        <section class="mx-auto max-w-6xl px-6 py-6">
            <h2 class="text-sm font-semibold uppercase tracking-widest text-slate-400">{{ t.ourCategories }}</h2>
            <div class="mt-4 flex flex-wrap gap-3">
                <div
                    v-for="category in categories"
                    :key="category.slug"
                    class="group flex items-center gap-3 rounded-2xl border border-black/5 bg-white px-5 py-3 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md"
                >
                    <span class="text-xl">{{ emojiFor(category.slug) }}</span>
                    <div>
                        <p class="font-semibold leading-none">{{ category.name[locale] }}</p>
                        <p class="mt-1 text-xs text-slate-400">{{ t.items(category.productCount) }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Products -->
        <section id="products" class="mx-auto max-w-6xl px-6 py-12">
            <h2 class="text-2xl font-bold tracking-tight">{{ t.featured }}</h2>
            <div class="mt-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <article
                    v-for="(product, i) in products"
                    :key="product.slug"
                    class="group overflow-hidden rounded-3xl border border-black/5 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-xl"
                >
                    <!-- Cover -->
                    <div
                        class="relative flex h-44 items-center justify-center bg-gradient-to-br"
                        :class="accents[i % accents.length]"
                    >
                        <span class="text-6xl drop-shadow-sm">{{ emojiFor(product.slug) }}</span>
                        <span
                            v-if="product.onSale"
                            class="absolute left-4 top-4 rounded-full bg-white/95 px-3 py-1 text-xs font-bold tracking-wide text-rose-600 shadow"
                        >{{ t.sale }}</span>
                        <span
                            v-if="product.variantCount > 1"
                            class="absolute bottom-4 right-4 rounded-full bg-black/25 px-3 py-1 text-xs font-semibold text-white backdrop-blur"
                        >{{ t.options(product.variantCount) }}</span>
                    </div>
                    <!-- Body -->
                    <div class="p-6">
                        <p class="text-xs font-semibold uppercase tracking-widest text-indigo-500">{{ product.category[locale] }}</p>
                        <h3 class="mt-1 text-lg font-bold leading-snug">{{ product.name[locale] }}</h3>
                        <p v-if="specsLine(product.specs)" class="mt-1 text-sm text-slate-400">{{ specsLine(product.specs) }}</p>
                        <div class="mt-4 flex items-baseline gap-1">
                            <span class="text-xs text-slate-400">{{ t.from }}</span>
                            <span class="text-xl font-extrabold text-slate-900">{{ price(product.priceFrom) }}</span>
                        </div>
                    </div>
                </article>
            </div>
        </section>

        <!-- Footer -->
        <footer class="mt-12 border-t border-black/5 py-10 text-center">
            <p class="text-lg font-extrabold tracking-tight">Planora</p>
            <p class="mt-2 text-sm text-slate-400">{{ t.footer }}</p>
        </footer>
    </div>
</template>
