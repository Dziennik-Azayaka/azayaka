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

import SessionApiService from "@/api/services/session";
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
        title: "home",
        link: { name: "home" },
        icon: LucideHome,
    },
    {
        title: "accountData",
        link: { name: "data" },
        icon: LucideIdCard,
    },
    {
        title: "activityHistory",
        link: { name: "activity" },
        icon: LucideHistory,
    },
];

syncLocaleWithStore(storeLocale, i18nLocale);

const route = useRoute();
const router = useRouter();

useTitle(
    computed(() => `${t(route.meta.title as string)} - ${t("accountSettings")}`),
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

async function getSessionInfo() {
    loading.value = true;
    error.value = false;
    try {
        const result = await SessionApiService.getSessionInfo();
        if (!result.loggedIn) return await router.push("/");
        mainStore.emailAddress = result.emailAddress;
    } catch {
        error.value = true;
    } finally {
        loading.value = false;
    }
}

onBeforeMount(getSessionInfo);

const appVersion = import.meta.env.VITE_APP_VERSION;
</script>

<template>
    <div class="w-screen h-dvh flex flex-col items-center justify-center" v-if="loading || error">
        <LucideLoaderCircle class="animate-spin mx-auto" :aria-label="t('pleaseWait')" v-if="loading" />
        <template v-else>
            <LucideAlertCircle class="mx-auto size-20" />
            <p class="text-center mt-3 font-medium text-lg">{{ t("unknownError") }}</p>
            <Button class="mx-auto mt-8" variant="outline" @click="getSessionInfo" size="lg">
                <RefreshCw />
                {{ t("tryAgain") }}
            </Button>
        </template>
    </div>
    <PanelLayout v-else>
        <template #navigation>
            <PanelNavigation v-model="mainStore.mobileNavOpen">
                <template #header>
                    <PanelNavigationHeaderMenu
                        :title="t('accountSettings')"
                        :subtitle="mainStore.emailAddress!"
                        current-app="myAccount"
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
