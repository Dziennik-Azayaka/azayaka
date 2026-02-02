<script setup lang="ts">
import PanelNavigationHeaderMenuTrigger from "./PanelNavigationHeaderMenuTrigger.vue";
import { useMediaQuery } from "@vueuse/core";
import {
    LucideBookCopy,
    LucideBookMarked,
    LucideBuilding,
    LucideGraduationCap,
    LucideSettings2,
    LucideUserCog,
} from "lucide-vue-next";
import { type Component } from "vue";
import { useI18n } from "vue-i18n";

import { Button } from "#ui/components/ui/button";
import {
    Dialog,
    DialogClose,
    DialogContent,
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

type Module = "myAccount" | "administrator" | "secretary" | "register" | "student" | "teacher";

interface Access {
    id: number;
    name: string;
    type: "employee" | "guardian" | "student";
    modules: Module[];
}

defineProps<{
    title: string;
    subtitle?: string;
    accesses: Access[];
}>();

const isMobile = useMediaQuery("(width < 80rem)");
const { t } = useI18n();

const moduleIcons: Record<Module, Component> = {
    myAccount: LucideUserCog,
    administrator: LucideSettings2,
    secretary: LucideBuilding,
    register: LucideBookCopy,
    student: LucideGraduationCap,
    teacher: LucideBookMarked,
};
const moduleRootURLs: Record<Module, string> = {
    myAccount: "/myaccount",
    administrator: "/administrator",
    secretary: "/secretary",
    register: "/register",
    student: "/student",
    teacher: "/teacher",
};

const getLocation = () => window.location.pathname;
</script>

<template>
    <DropdownMenu v-if="!isMobile">
        <DropdownMenuTrigger as-child>
            <PanelNavigationHeaderMenuTrigger :title="title" :subtitle="subtitle" />
        </DropdownMenuTrigger>
        <DropdownMenuContent class="w-[21.875rem]">
            <DropdownMenuLabel>{{ t("goTo") }}</DropdownMenuLabel>
            <DropdownMenuItem as-child>
                <a
                    :href="moduleRootURLs['myAccount']"
                    target="_blank"
                    :title="t('openInNewCardInfo')"
                    class="cursor-pointer"
                    :class="{
                        '!bg-primary !text-primary-foreground *:!text-primary-foreground': getLocation().startsWith(
                            moduleRootURLs['myAccount'],
                        ),
                    }"
                >
                    <component :is="moduleIcons['myAccount']" class="text-foreground" />
                    {{ t(`apps.myAccount`) }}
                </a>
            </DropdownMenuItem>
            <template v-for="access in accesses" :key="access.id">
                <p class="px-2 py-1.5 text-xs font-medium text-muted-foreground">
                    {{ access.name }} ({{ t(access.type) }})
                </p>
                <DropdownMenuItem v-for="module in access.modules" :key="module" as-child>
                    <a
                        :href="`${moduleRootURLs[module]}/${access.id}`"
                        target="_blank"
                        :title="t('openInNewCardInfo')"
                        class="cursor-pointer"
                        :class="{
                            '!bg-primary !text-primary-foreground *:!text-primary-foreground': getLocation().startsWith(
                                `${moduleRootURLs[module]}/${access.id}`,
                            ),
                        }"
                    >
                        <component :is="moduleIcons[module]" class="text-foreground" />
                        {{ t(`apps.${module}`) }}
                    </a>
                </DropdownMenuItem>
            </template>
        </DropdownMenuContent>
    </DropdownMenu>
    <Dialog v-else>
        <DialogTrigger>
            <PanelNavigationHeaderMenuTrigger :title="title" :subtitle="subtitle" />
        </DialogTrigger>
        <DialogContent>
            <DialogHeader>
                <DialogTitle>{{ t("goTo") }}</DialogTitle>
            </DialogHeader>
            <ul class="space-y-1.5">
                <li>
                    <a
                        :href="moduleRootURLs['myAccount']"
                        target="_blank"
                        :title="t('openInNewCardInfo')"
                        class="px-4 py-3 rounded-md flex items-center gap-3 font-medium text-sm hover:bg-accent transition-colors"
                    >
                        <component :is="moduleIcons['myAccount']" class="text-foreground" />
                        {{ t(`apps.myAccount`) }}
                    </a>
                </li>
                <li v-for="access in accesses" :key="access.id">
                    <p class="px-2 py-1.5 text-sm font-medium text-muted-foreground">
                        {{ access.name }} ({{ t(access.type) }})
                    </p>
                    <ul class="space-y-0.5">
                        <li v-for="module in access.modules" :key="module">
                            <a
                                :href="`${moduleRootURLs[module]}/${access.id}`"
                                target="_blank"
                                :title="t('openInNewCardInfo')"
                                class="px-4 py-3 rounded-md flex items-center gap-3 font-medium text-sm hover:bg-accent transition-colors"
                            >
                                <component :is="moduleIcons[module]" class="text-foreground" />
                                {{ t(`apps.${module}`) }}
                            </a>
                        </li>
                    </ul>
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
