<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import StorefrontLayout from '../Layouts/StorefrontLayout.vue';
import { useLocale } from '../composables/useLocale';

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
    image: string | null; // primary media path, or null → emoji fallback
    priceFrom: number; // cents — what the customer pays (current)
    regularPrice: number; // cents — pre-sale price, for the struck-through
    onSale: boolean;
    variantCount: number;
    specs: Record<string, string | number> | null;
}

defineProps<{
    categories: Category[];
    products: Product[];
}>();

// Shared locale ref — the header toggle in StorefrontLayout flips this same value.
const { locale } = useLocale();

// Homepage-specific copy. Header/footer strings live in StorefrontLayout.
// NOTE: hero + info-band text is PLACEHOLDER — the shop owner refines it later.
const ui = {
    en: {
        heroSub: 'Thoughtfully designed planners, notebooks and stickers to make every day feel intentional.',
        shop: 'Shop the collection',
        ourCategories: 'Browse by category',
        featured: 'Featured products',
        from: 'from',
        sale: 'SALE',
        options: (n: number) => (n > 1 ? `${n} options` : '1 option'),
        items: (n: number) => (n === 1 ? '1 product' : `${n} products`),
        whyTitle: 'Made to be lived in',
        pillars: [
            { icon: '🌿', title: 'Thoughtful by design', text: 'Every layout is drawn to give your days room to breathe — not another rigid grid.' },
            { icon: '✍️', title: 'Made for real routines', text: 'Planners that adapt to how you actually live, work and dream.' },
            { icon: '💛', title: 'Crafted with care', text: 'Small batches, quality paper, and details you feel the moment you open the cover.' },
        ],
    },
    bg: {
        heroSub: 'Внимателно създадени тефтери, бележници и стикери, които правят всеки ден осмислен.',
        shop: 'Разгледай колекцията',
        ourCategories: 'Разгледай по категория',
        featured: 'Препоръчани продукти',
        from: 'от',
        sale: 'НАМАЛЕНИЕ',
        options: (n: number) => (n > 1 ? `${n} опции` : '1 опция'),
        items: (n: number) => (n === 1 ? '1 продукт' : `${n} продукта`),
        whyTitle: 'Създадени, за да ги живееш',
        pillars: [
            { icon: '🌿', title: 'Обмислени до детайл', text: 'Всяко оформление е създадено да дава простор на дните ти — а не поредната скована таблица.' },
            { icon: '✍️', title: 'За истинско ежедневие', text: 'Планери, които се нагаждат към начина, по който живееш, работиш и мечтаеш.' },
            { icon: '💛', title: 'Изработени с грижа', text: 'Малки серии, качествена хартия и детайли, които усещаш още с отварянето на корицата.' },
        ],
    },
} as const;

const t = computed(() => ui[locale.value]);

// A palette of soft, playful gradients for products without an uploaded image.
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

// Discount as a rounded whole percent, e.g. 1999 -> 999 = 50. Guarded so a
// missing/invalid regular price can't produce NaN or a nonsense badge.
const discountPct = (regular: number, current: number): number =>
    regular > current && regular > 0 ? Math.round((1 - current / regular) * 100) : 0;

// Glide image helpers — same on-the-fly WebP pipeline as the product page.
const img = (path: string, width: number): string => `/img/${path}?w=${width}&fm=webp`;
const srcset = (path: string): string =>
    [400, 800].map((w) => `${img(path, w)} ${w}w`).join(', ');

const specsLine = (specs: Product['specs']): string =>
    specs ? Object.values(specs).join(' · ') : '';
</script>

<template>
    <StorefrontLayout>
        <Head title="Planora — Planning, made beautiful" />

        <!-- Hero -->
        <section class="relative overflow-hidden">
            <div class="pointer-events-none absolute -left-24 -top-24 h-72 w-72 rounded-full bg-violet-300/40 blur-3xl"></div>
            <div class="pointer-events-none absolute -right-20 top-10 h-72 w-72 rounded-full bg-rose-300/40 blur-3xl"></div>
            <div class="relative mx-auto max-w-6xl px-6 py-20 text-center sm:py-28">
                <!-- Wordmark flanked by fluid line-art botanicals (mirrored SVG sprigs) -->
                <div class="flex items-center justify-center gap-2 sm:gap-6">
                    <!-- Left sprig: main stem + lower offshoot, six leaves, buds, a blossom -->
                    <svg class="w-20 shrink-0 sm:w-40" viewBox="0 0 160 80" fill="none" aria-hidden="true">
                        <defs>
                            <linearGradient id="floral-l" x1="0" y1="1" x2="1" y2="0">
                                <stop offset="0%" stop-color="#8b5cf6" />
                                <stop offset="50%" stop-color="#d946ef" />
                                <stop offset="100%" stop-color="#f43f5e" />
                            </linearGradient>
                        </defs>
                        <g stroke="url(#floral-l)" stroke-width="1.4" stroke-linecap="round">
                            <path d="M158 46 C 116 52, 84 44, 44 20" />
                            <path d="M120 49 C 100 60, 74 64, 52 56" />
                        </g>
                        <g fill="url(#floral-l)">
                            <path d="M120 45 C 117 33, 124 26, 134 28 C 128 35, 125 42, 120 45 Z" />
                            <path d="M97 39 C 100 51, 92 59, 81 56 C 88 49, 92 42, 97 39 Z" />
                            <path d="M78 33 C 75 21, 82 14, 92 16 C 86 23, 83 30, 78 33 Z" />
                            <path d="M60 25 C 57 13, 64 6, 74 8 C 68 15, 65 22, 60 25 Z" />
                            <path d="M104 53 C 107 64, 100 71, 90 69 C 96 63, 100 57, 104 53 Z" />
                            <path d="M84 58 C 81 69, 73 74, 64 71 C 71 66, 77 61, 84 58 Z" />
                            <circle cx="52" cy="56" r="2.6" />
                            <circle cx="132" cy="52" r="1.8" />
                            <circle cx="44" cy="20" r="4" />
                        </g>
                        <circle cx="44" cy="20" r="1.5" fill="#fff" fill-opacity="0.85" />
                    </svg>

                    <h1
                        style="font-family: 'Great Vibes', cursive"
                        class="bg-gradient-to-r from-rose-500 via-fuchsia-500 to-violet-500 bg-clip-text px-2 py-4 text-7xl leading-tight text-transparent sm:py-6 sm:text-9xl"
                    >Planora</h1>

                    <!-- Right sprig: the same motif, horizontally mirrored -->
                    <svg class="w-20 shrink-0 -scale-x-100 sm:w-40" viewBox="0 0 160 80" fill="none" aria-hidden="true">
                        <defs>
                            <linearGradient id="floral-r" x1="0" y1="1" x2="1" y2="0">
                                <stop offset="0%" stop-color="#8b5cf6" />
                                <stop offset="50%" stop-color="#d946ef" />
                                <stop offset="100%" stop-color="#f43f5e" />
                            </linearGradient>
                        </defs>
                        <g stroke="url(#floral-r)" stroke-width="1.4" stroke-linecap="round">
                            <path d="M158 46 C 116 52, 84 44, 44 20" />
                            <path d="M120 49 C 100 60, 74 64, 52 56" />
                        </g>
                        <g fill="url(#floral-r)">
                            <path d="M120 45 C 117 33, 124 26, 134 28 C 128 35, 125 42, 120 45 Z" />
                            <path d="M97 39 C 100 51, 92 59, 81 56 C 88 49, 92 42, 97 39 Z" />
                            <path d="M78 33 C 75 21, 82 14, 92 16 C 86 23, 83 30, 78 33 Z" />
                            <path d="M60 25 C 57 13, 64 6, 74 8 C 68 15, 65 22, 60 25 Z" />
                            <path d="M104 53 C 107 64, 100 71, 90 69 C 96 63, 100 57, 104 53 Z" />
                            <path d="M84 58 C 81 69, 73 74, 64 71 C 71 66, 77 61, 84 58 Z" />
                            <circle cx="52" cy="56" r="2.6" />
                            <circle cx="132" cy="52" r="1.8" />
                            <circle cx="44" cy="20" r="4" />
                        </g>
                        <circle cx="44" cy="20" r="1.5" fill="#fff" fill-opacity="0.85" />
                    </svg>
                </div>

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
                <!-- Each card links to its product page (client-side Inertia nav) -->
                <Link
                    v-for="(product, i) in products"
                    :key="product.slug"
                    :href="`/products/${product.slug}`"
                    class="group block overflow-hidden rounded-3xl border border-black/5 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-xl"
                >
                    <!-- Cover: real Glide image when available, else a soft gradient + emoji -->
                    <div class="relative aspect-[4/3] overflow-hidden">
                        <img
                            v-if="product.image"
                            :src="img(product.image, 600)"
                            :srcset="srcset(product.image)"
                            sizes="(min-width: 1024px) 33vw, (min-width: 640px) 50vw, 100vw"
                            :alt="product.name[locale]"
                            loading="lazy"
                            class="h-full w-full object-cover transition duration-300 group-hover:scale-105"
                        />
                        <div
                            v-else
                            class="flex h-full w-full items-center justify-center bg-gradient-to-br"
                            :class="accents[i % accents.length]"
                        >
                            <span class="text-6xl drop-shadow-sm">{{ emojiFor(product.slug) }}</span>
                        </div>

                        <span
                            v-if="product.onSale && product.regularPrice > product.priceFrom"
                            class="absolute left-4 top-4 rounded-full bg-rose-600 px-3 py-1 text-xs font-bold tracking-wide text-white shadow"
                        >-{{ discountPct(product.regularPrice, product.priceFrom) }}%</span>
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
                        <div class="mt-4 flex items-baseline gap-2">
                            <span class="text-xs text-slate-400">{{ t.from }}</span>
                            <template v-if="product.onSale && product.regularPrice > product.priceFrom">
                                <span class="text-sm text-slate-400 line-through">{{ price(product.regularPrice) }}</span>
                                <span class="text-xl font-extrabold text-rose-600">{{ price(product.priceFrom) }}</span>
                            </template>
                            <span v-else class="text-xl font-extrabold text-slate-900">{{ price(product.priceFrom) }}</span>
                        </div>
                    </div>
                </Link>
            </div>
        </section>

        <!-- Info band: why Planora (PLACEHOLDER copy — the shop owner refines) -->
        <section class="border-y border-black/5 bg-white/60">
            <div class="mx-auto max-w-6xl px-6 py-16">
                <h2 class="text-center text-2xl font-bold tracking-tight sm:text-3xl">{{ t.whyTitle }}</h2>
                <div class="mt-10 grid grid-cols-1 gap-8 sm:grid-cols-3">
                    <div v-for="pillar in t.pillars" :key="pillar.title" class="text-center">
                        <div class="text-4xl">{{ pillar.icon }}</div>
                        <h3 class="mt-4 text-lg font-bold">{{ pillar.title }}</h3>
                        <p class="mx-auto mt-2 max-w-xs text-sm leading-relaxed text-slate-500">{{ pillar.text }}</p>
                    </div>
                </div>
            </div>
        </section>
    </StorefrontLayout>
</template>
