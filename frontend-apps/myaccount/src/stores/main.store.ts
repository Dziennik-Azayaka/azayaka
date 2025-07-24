import { useColorMode, useStorage } from "@vueuse/core";
import { defineStore } from "pinia";
import { ref } from "vue";

export const useMainStore = defineStore("main", () => {
    const fontSize = useStorage<"normal" | "large">("a11y-font-size", "normal");
    const locale = useStorage<string>("locale", navigator.language.split("-")[0]);
    const colorMode = useColorMode({
        modes: {
            highContrast: "high-contrast",
        },
    });
    const mobileNavOpen = ref(false);
    const emailAddress = ref<string | null>(null);

    return { fontSize, locale, colorMode, mobileNavOpen, emailAddress };
});
