<script setup lang="ts">
import { LucideMenu } from "lucide-vue-next";
import { useI18n } from "vue-i18n";
import type { RouteLocationAsPathGeneric, RouteLocationAsRelativeGeneric } from "vue-router";

import {
    Breadcrumb,
    BreadcrumbItem,
    BreadcrumbList,
    BreadcrumbSeparator,
    Button,
    PanelAccountMenu,
} from "@azayaka-frontend/ui";

import { useMainStore } from "@/stores/main.store";

const mainStore = useMainStore();
const { locale, t } = useI18n();

defineProps<{
    breadcrumbPath: { href: string | RouteLocationAsPathGeneric | RouteLocationAsRelativeGeneric; title: string }[];
}>();
</script>

<template>
    <div class="mb-6 flex items-center gap-3">
        <Button variant="ghost" size="icon" @click="mainStore.mobileNavOpen = true" class="xl:hidden">
            <LucideMenu />
        </Button>
        <img
            class="h-5 xl:hidden"
            src="https://tailwindcss.com/plus-assets/img/logos/mark.svg?color=blue&shade=600"
            alt="Logo (tymczasowo tailwind)"
        />
        <Breadcrumb class="not-xl:hidden">
            <BreadcrumbList>
                <BreadcrumbItem>
                    <img
                        class="h-5"
                        src="https://tailwindcss.com/plus-assets/img/logos/mark.svg?color=blue&shade=600"
                        alt="Logo (tymczasowo tailwind)"
                    />
                </BreadcrumbItem>
                <BreadcrumbSeparator />
                <BreadcrumbItem> {{ t("accountSettings") }} </BreadcrumbItem>
                <template v-for="({ href, title }, index) in breadcrumbPath" :key="index">
                    <BreadcrumbSeparator />
                    <BreadcrumbItem :class="{ 'text-primary': index === breadcrumbPath.length - 1 }">
                        <RouterLink :to="href">{{ title }}</RouterLink>
                    </BreadcrumbItem>
                </template>
            </BreadcrumbList>
        </Breadcrumb>
        <div class="flex-1" />
        <PanelAccountMenu
            v-if="mainStore.emailAddress"
            v-model:theme="mainStore.colorMode"
            v-model:lang="locale"
            v-model:font-size="mainStore.fontSize"
            :email-address="mainStore.emailAddress"
        />
    </div>
</template>
