import { useColorMode, useStorage } from "@vueuse/core";
import { defineStore } from "pinia";

export const useMainStore = defineStore("main", () => {
    const fontSize = useStorage<"normal" | "large">("a11y-font-size", "normal");
    const locale = useStorage<string>("locale", navigator.language);
    const colorMode = useColorMode({
        modes: {
            highContrast: "high-contrast",
        },
    });

    return { fontSize, locale, colorMode };
});
