<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';
import StorefrontLayout from '../../Layouts/StorefrontLayout.vue';
import { useLocale } from '../../composables/useLocale';

interface Translated {
    bg: string;
    en: string;
}

interface Product {
    slug: string;
    name: Translated;
    description: Translated | null;
    category: Translated | null;
    price: number | null; // cents
    regularPrice: number | null; // cents
    onSale: boolean;
    inStock: boolean;
    specs: Record<string, string | number> | null;
    image: string | null; // storage path, e.g. "products/xxx.png"
}

const props = defineProps<{ product: Product }>();

const { locale } = useLocale();

// cents -> "€12.90"
const euro = (cents: number | null): string =>
    cents === null ? '' : `€${(cents / 100).toFixed(2)}`;

// Build a Glide URL (WebP) for a given width.
const img = (width: number): string =>
    props.product.image ? `/img/${props.product.image}?w=${width}&fm=webp` : '';

// Responsive srcset: widths the browser can pick from.
const srcset = computed(() =>
    props.product.image ? [400, 800, 1200].map((w) => `${img(w)} ${w}w`).join(', ') : '',
);
</script>

<template>
    <StorefrontLayout>
        <Head :title="product.name[locale]" />

        <section class="mx-auto grid max-w-6xl gap-10 px-6 py-12 lg:grid-cols-2">
            <!-- ── WORKED EXAMPLE: responsive image via Glide ──────────────── -->
            <div class="aspect-[5/5] overflow-hidden rounded-3xl bg-white shadow-sm">
                <img
                    v-if="product.image"
                    :src="img(800)"
                    :srcset="srcset"
                    sizes="(min-width: 1024px) 40vw, 100vw"
                    :alt="product.name[locale]"
                    class="h-full w-full object-cover"
                />
                <div v-else class="flex aspect-square items-center justify-center text-7xl">📓</div>
            </div>

            <!-- Details -->
            <div class="flex flex-col justify-center">
                <!-- WORKED EXAMPLE: category (only if present) -->
                <p
                    v-if="product.category"
                    class="text-xs font-semibold uppercase tracking-widest text-indigo-500"
                >{{ product.category[locale] }}</p>

                <!-- WORKED EXAMPLE: product name -->
                <h1 class="mt-2 text-4xl font-bold leading-tight">{{ product.name[locale] }}</h1>
                
                <div class="mt-4 flex items-baseline gap-3 text-2xl font-extrabold">
                    <template v-if="product.onSale">
                        <span class="text-slate-400 line-through">{{ euro(product.regularPrice) }}</span>
                        <span class="text-rose-600">{{ euro(product.price) }}</span>
                    </template>
                    <span v-else>{{ euro(product.price) }}</span>
                </div>   
                
                <p v-if="product.description" class="mt-6 leading-relaxed text-slate-600">
                    {{ product.description[locale] }}
                </p>

                <button
                    :disabled="!product.inStock"
                    class="mt-8 rounded-full bg-slate-900 px-8 py-3.5 text-sm font-semibold text-white disabled:opacity-40"
                >
                    <template v-if="product.inStock">{{ locale === 'bg' ? 'Добави в кошницата' : 'Add to cart' }}</template>
                    <template v-else>{{ locale === 'bg' ? 'Няма наличност' : 'Out of Stock'}}</template>
                 </button>
            </div>
        </section>
    </StorefrontLayout>
</template>
