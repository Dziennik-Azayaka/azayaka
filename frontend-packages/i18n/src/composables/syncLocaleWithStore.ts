import { onBeforeMount, watch } from "vue";
import type { Ref } from "vue";

export default (storeLocaleRef: Ref<string, string>, i18nLocaleRef: Ref<string, string>) => {
    watch(i18nLocaleRef, () => {
        const parentEl = document.querySelector("html");
        if (parentEl) parentEl.lang = i18nLocaleRef.value;
        storeLocaleRef.value = i18nLocaleRef.value;
    });

    onBeforeMount(() => {
        i18nLocaleRef.value = storeLocaleRef.value;
    });
};
