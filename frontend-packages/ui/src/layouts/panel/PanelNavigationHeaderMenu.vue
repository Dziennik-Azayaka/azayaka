<script setup lang="ts">
import PanelNavigationHeaderMenuTrigger from "./PanelNavigationHeaderMenuTrigger.vue";
import { useMediaQuery } from "@vueuse/core";
import { LucideUserCog } from "lucide-vue-next";
import { type Component, ref } from "vue";
import { useI18n } from "vue-i18n";

import { Button } from "#ui/components/ui/button";
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from "#ui/components/ui/dialog";
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuTrigger,
} from "#ui/components/ui/dropdown-menu";

defineProps<{
    title: string;
    subtitle?: string;
}>();

const isMobile = useMediaQuery("(width < 80rem)");
const { t } = useI18n();

type App = "myAccount";

const appIcons: Record<App, Component> = {
    myAccount: LucideUserCog,
};
const appRootURLs: Record<App, string> = {
    myAccount: "http://localhost:5002",
};

const userApps = ref<App[]>(["myAccount"]);
</script>

<template>
    <DropdownMenu v-if="!isMobile">
        <DropdownMenuTrigger as-child>
            <PanelNavigationHeaderMenuTrigger :title="title" :subtitle="subtitle" />
        </DropdownMenuTrigger>
        <DropdownMenuContent class="w-[21.875rem]">
            <DropdownMenuLabel>{{ t("goTo") }}</DropdownMenuLabel>
            <p class="px-2 py-1.5 text-xs font-medium text-muted-foreground">LO 23 Gdańsk</p>
            <DropdownMenuItem v-for="app in userApps" :key="app" as-child>
                <a
                    :href="appRootURLs[app]"
                    target="_blank"
                    title="Odnośnik otwiera się w nowej karcie"
                    class="cursor-pointer"
                >
                    <component :is="appIcons[app]" class="text-foreground" />
                    {{ t(`apps.${app}`) }}
                </a>
            </DropdownMenuItem>
        </DropdownMenuContent>
    </DropdownMenu>
    <Dialog v-else>
        <DialogTrigger>
            <PanelNavigationHeaderMenuTrigger :title="title" :subtitle="subtitle" />
        </DialogTrigger>
        <DialogContent>
            <DialogHeader>
                <DialogTitle>{{ t("goTo") }}</DialogTitle>
                <DialogDescription>LO 23 Gdańsk</DialogDescription>
            </DialogHeader>
            <ul class="space-y-0.5">
                <li v-for="app in userApps" :key="app">
                    <a
                        :href="appRootURLs[app]"
                        target="_blank"
                        title="Odnośnik otwiera się w nowej karcie"
                        class="px-4 py-3 rounded-md flex items-center gap-3 font-medium text-sm hover:bg-accent transition-colors"
                    >
                        <component :is="appIcons[app]" class="text-foreground" />
                        {{ t(`apps.${app}`) }}
                    </a>
                </li>
            </ul>
            <DialogFooter>
                <DialogClose as-child>
                    <Button variant="outline">{{ t("close") }}</Button>
                </DialogClose>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
