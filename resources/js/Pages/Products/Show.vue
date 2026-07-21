<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
// Swiper: the Vue components, the feature modules we use, and their CSS.
import { Swiper, SwiperSlide } from 'swiper/vue';
import { Keyboard, Navigation, Thumbs, Zoom } from 'swiper/modules';
import type { Swiper as SwiperClass } from 'swiper';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/thumbs';
import 'swiper/css/zoom';
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
    images: string[]; // ordered storage paths; [0] is the primary
}

const props = defineProps<{ product: Product }>();

const { locale } = useLocale();

// cents -> "€12.90"
const euro = (cents: number | null): string =>
    cents === null ? '' : `€${(cents / 100).toFixed(2)}`;

// Build a Glide URL (WebP) for a given image path + width.
const img = (path: string, width: number): string => `/img/${path}?w=${width}&fm=webp`;

// Responsive srcset for one image: widths the browser can pick from.
const srcset = (path: string): string =>
    [400, 800, 1200].map((w) => `${img(path, w)} ${w}w`).join(', ');

// The thumbnail Swiper instance — the main Swiper links to it so clicking a
// thumbnail navigates the main image (and the active thumb highlights).
// Loosely typed (`any`): Swiper's own recursive types reject a Swiper instance
// in `thumbs.swiper` (a known upstream typing issue).
const thumbsSwiper = ref<any>(null);
const setThumbsSwiper = (swiper: SwiperClass): void => {
    thumbsSwiper.value = swiper;
};

// Show up to 5 thumbnails across; fewer if there are fewer images.
const thumbCount = computed(() => Math.min(props.product.images.length, 5));
</script>

<template>
    <StorefrontLayout>
        <Head :title="product.name[locale]" />

        <section class="mx-auto grid max-w-6xl gap-10 px-6 py-12 lg:grid-cols-2">
            <!-- ── GALLERY ─────────────────────────────────────────────────── -->
            <div>
                <template v-if="product.images.length">
                    <!-- Main image: arrows (Navigation), swipe (built-in), zoom (Zoom module) -->
                    <Swiper
                        :modules="[Navigation, Thumbs, Zoom, Keyboard]"
                        :navigation="true"
                        :zoom="true"
                        :keyboard="{ enabled: true }"
                        :thumbs="{ swiper: thumbsSwiper }"
                        :space-between="10"
                        class="aspect-square overflow-hidden rounded-3xl bg-white shadow-sm"
                    >
                        <SwiperSlide v-for="path in product.images" :key="path">
                            <!-- swiper-zoom-container is what the Zoom module scales on click/pinch -->
                            <div class="swiper-zoom-container h-full w-full">
                                <img
                                    :src="img(path, 800)"
                                    :srcset="srcset(path)"
                                    sizes="(min-width: 1024px) 40vw, 100vw"
                                    :alt="product.name[locale]"
                                    class="h-full w-full object-cover"
                                    loading="lazy"
                                />
                            </div>
                        </SwiperSlide>
                    </Swiper>

                    <!-- Thumbnails: only when there's more than one image -->
                    <Swiper
                        v-if="product.images.length > 1"
                        :modules="[Thumbs]"
                        :slides-per-view="thumbCount"
                        :space-between="10"
                        :watch-slides-progress="true"
                        class="mt-3"
                        @swiper="setThumbsSwiper"
                    >
                        <SwiperSlide v-for="path in product.images" :key="'thumb-' + path" class="cursor-pointer">
                            <img
                                :src="img(path, 200)"
                                :alt="product.name[locale]"
                                loading="lazy"
                                class="aspect-square w-full rounded-xl object-cover opacity-60 transition [.swiper-slide-thumb-active_&]:opacity-100"
                            />
                        </SwiperSlide>
                    </Swiper>
                </template>

                <!-- No images uploaded -->
                <div
                    v-else
                    class="flex aspect-square items-center justify-center rounded-3xl bg-white text-7xl shadow-sm"
                >📓</div>
            </div>

            <!-- Details -->
            <div class="flex flex-col justify-center">
                <!-- category (only if present) -->
                <p
                    v-if="product.category"
                    class="text-xs font-semibold uppercase tracking-widest text-indigo-500"
                >{{ product.category[locale] }}</p>

                <!-- product name -->
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
