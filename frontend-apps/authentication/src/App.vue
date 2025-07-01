<script setup lang="ts">
import { useElementSize, useTitle } from "@vueuse/core";
import { storeToRefs } from "pinia";
import { configure } from "vee-validate";
import { computed, useTemplateRef, watch } from "vue";
import { useI18n } from "vue-i18n";
import { useRoute } from "vue-router";

import { syncLocaleWithStore } from "@azayaka-frontend/i18n";
import SkipToMain from "@azayaka-frontend/ui/src/components/ui/a11y/SkipToMain.vue";

import A11yFontSize from "@/components/A11yFontSize.vue";
import AuthenticationIllustration from "@/components/AuthenticationIllustration.vue";
import LanguageSwitcher from "@/components/LanguageSwitcher.vue";
import ThemeSwitcher from "@/components/ThemeSwitcher.vue";
import { useMainStore } from "@/stores/main.store";

const view = useTemplateRef("view");
const { height: cardHeight } = useElementSize(view);
const route = useRoute();
const { t, locale: i18nLocale } = useI18n();
const mainStore = useMainStore();

configure({
    validateOnBlur: false,
    validateOnChange: false,
    validateOnInput: false,
    validateOnModelUpdate: false,
});
const { locale: storeLocale } = storeToRefs(mainStore);
syncLocaleWithStore(storeLocale, i18nLocale);

useTitle(
    computed(() => t(route.meta.title as string)),
    { titleTemplate: "Dziennik Azyaka | %s" },
);

watch(
    () => mainStore.fontSize,
    () => {
        const parentEl = document.querySelector("html");
        if (parentEl) parentEl.style.fontSize = mainStore.fontSize === "large" ? "22px" : "16px";
    },
    { immediate: true },
);

const appVersion = import.meta.env.VITE_APP_VERSION;
</script>

<template>
    <SkipToMain />
    <div class="flex flex-col min-h-dvh max-w-[80rem] mx-auto py-3 md:py-5 px-6 md:px-12 gap-10">
        <section class="flex gap-5">
            <A11yFontSize />
            <ThemeSwitcher />
            <div class="flex-1" />
            <LanguageSwitcher />
        </section>
        <div class="flex-1 not-md:hidden" />
        <div class="flex items-center justify-center xl:justify-between gap-10">
            <div class="space-y-12 md:max-w-[34rem] w-full">
                <header class="not-sm:flex-col flex gap-3 sm:items-center">
                    <div class="size-7 p-2 rounded-md bg-primary box-content" aria-hidden="true">
                        <img
                            class="size-7"
                            src="https://tailwindcss.com/plus-assets/img/logos/mark.svg?color=blue&shade=100"
                            alt="Logo (tymczasowo tailwind)"
                            v-if="mainStore.colorMode !== 'dark'"
                        />
                        <img
                            class="size-7"
                            src="https://tailwindcss.com/plus-assets/img/logos/mark.svg?color=zinc&shade=900"
                            alt="Logo (tymczasowo tailwind)"
                            v-else
                        />
                    </div>
                    <div>
                        <p class="text-lg font-semibold">Liceum Ogólnokształcące nr 3 w Gdańsku</p>
                        <p class="text-sm text-foreground/70">Dziennik Azayaka</p>
                    </div>
                </header>
                <main
                    id="main-content"
                    class="transition-[height]"
                    :style="{
                        height: cardHeight ? cardHeight + 'px' : undefined,
                    }"
                >
                    <div ref="view">
                        <RouterView v-slot="{ Component }">
                            <transition name="fade" mode="out-in">
                                <component :is="Component" />
                            </transition>
                        </RouterView>
                    </div>
                </main>
            </div>
            <AuthenticationIllustration class="not-xl:hidden max-w-[34rem] flex-1" role="presentation" />
        </div>
        <div class="flex-1" />
        <footer class="flex justify-between text-sm text-muted-foreground">
            <p><span class="not-sm:hidden">Dziennik Azayaka</span> {{ appVersion }} &copy; 2025</p>
            <ul>
                <li>
                    <a href="#" class="hover:underline">{{ t("privacyPolicy") }}</a>
                </li>
            </ul>
        </footer>
    </div>
</template>

<style lang="css" scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease-in;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
