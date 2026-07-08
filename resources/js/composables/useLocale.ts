import { ref } from 'vue';

export type Locale = 'bg' | 'en';

// Module-level ref = a single shared value across the whole app, so the header's
// language toggle and every page stay in sync.
const locale = ref<Locale>('bg');

export function useLocale() {
    return { locale };
}
