<script setup lang="ts">
import { useTitle } from "@vueuse/core";
import {
    LucideAlertCircle,
    LucideHistory,
    LucideHome,
    LucideIdCard,
    LucideLoaderCircle,
    RefreshCw,
} from "lucide-vue-next";
import { storeToRefs } from "pinia";
import { configure } from "vee-validate";
import { computed, onBeforeMount, ref, watch } from "vue";
import { useI18n } from "vue-i18n";
import { useRoute, useRouter } from "vue-router";

import { syncLocaleWithStore } from "@azayaka-frontend/i18n";
import { Button, PanelNavigation, PanelNavigationHeaderMenu } from "@azayaka-frontend/ui";
import { PanelLayout, PanelNavigationItem } from "@azayaka-frontend/ui";

import { useMainStore } from "@/stores/main.store";

configure({
    validateOnBlur: false,
    validateOnChange: false,
    validateOnInput: false,
    validateOnModelUpdate: false,
});

const mainStore = useMainStore();
const { t, locale: i18nLocale } = useI18n();

const { locale: storeLocale } = storeToRefs(mainStore);

const menuItems = [
    {
        title: "helloWorld",
        link: { name: "helloWorld" },
        icon: LucideHome,
    },
];

syncLocaleWithStore(storeLocale, i18nLocale);

const route = useRoute();
const router = useRouter();

useTitle(
    computed(() => `${t(route.meta.title as string)} - ${t("administrator")}`),
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

const loading = ref(false);
const error = ref(false);

const appVersion = import.meta.env.VITE_APP_VERSION;
</script>

<template>
    <PanelLayout>
        <template #navigation>
            <PanelNavigation v-model="mainStore.mobileNavOpen">
                <template #header>
                    <PanelNavigationHeaderMenu :title="t('administrator')" current-app="administrator" />
                </template>
                <template #navigation-top>
                    <PanelNavigationItem
                        v-for="({ title, icon, link }, index) in menuItems"
                        :key="index"
                        :title="t(title)"
                        :icon="icon"
                        :link="link"
                        @click="mainStore.mobileNavOpen = false"
                    />
                </template>
                <!--
                <template #navigation-bottom>
                    <PanelNavigationItem title="Pomoc" :icon="LucideHelpCircle" link="/help" />
                </template>
                -->
                <template #footer>
                    <div class="flex justify-between">
                        <span>Dziennik Azayaka</span>
                        <span>{{ appVersion }}</span>
                    </div>
                </template>
            </PanelNavigation>
        </template>
        <template #content>
            <RouterView v-slot="{ Component }">
                <transition name="fade" mode="out-in">
                    <component :is="Component" />
                </transition>
            </RouterView>
        </template>
    </PanelLayout>
</template>

<style lang="css" scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.15s ease-in;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
