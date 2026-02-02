<script setup lang="ts">
import { useQueryClient } from "@tanstack/vue-query";
import { useTitle } from "@vueuse/core";
import { LucideLoaderCircle } from "lucide-vue-next";
import { storeToRefs } from "pinia";
import { computed, watch } from "vue";
import { useI18n } from "vue-i18n";
import { useRoute } from "vue-router";

import { syncLocaleWithStore } from "@azayaka-frontend/i18n";
import { PanelNavigation, PanelNavigationHeaderMenu } from "@azayaka-frontend/ui";
import { PanelLayout, PanelNavigationItem } from "@azayaka-frontend/ui";

import { menuItems } from "@/navigation";
import { useMainStore } from "@/stores/main.store";
import { useUserStore } from "@/stores/user.store";

const mainStore = useMainStore();
const userStore = useUserStore();
const { t, locale: i18nLocale } = useI18n();
const route = useRoute();

const { locale: storeLocale } = storeToRefs(mainStore);

useQueryClient();
syncLocaleWithStore(storeLocale, i18nLocale);
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

const appVersion = import.meta.env.VITE_APP_VERSION;
</script>

<template>
    <div class="w-screen h-dvh flex flex-col items-center justify-center" v-if="!userStore.user">
        <LucideLoaderCircle class="animate-spin mx-auto" :aria-label="t('pleaseWait')" />
    </div>
    <PanelLayout v-else>
        <template #navigation>
            <PanelNavigation v-model="mainStore.mobileNavOpen">
                <template #header>
                    <PanelNavigationHeaderMenu
                        :title="t('administrator')"
                        :subtitle="userStore.access?.name ?? userStore.user.emailAddress"
                        current-app="administrator"
                        :accesses="userStore.user.accesses"
                    />
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
