<script setup lang="ts">
import { useMediaQuery } from "@vueuse/core";
import { LucideMenu } from "lucide-vue-next";
import { useI18n } from "vue-i18n";

import { Button } from "#ui/components/ui/button";

const isMobile = useMediaQuery("(width < 80rem)");
const mobileOpen = defineModel({ default: false });
const { t } = useI18n();
</script>

<template>
    <Transition
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div
            class="z-10 fixed top-0 left-0 right-0 bottom-0 bg-foreground/20 dark:bg-background/60 transition-[opacity] duration-300"
            aria-hidden="true"
            v-if="mobileOpen && isMobile"
            @click="mobileOpen = !mobileOpen"
        />
    </Transition>
    <Transition
        enter-from-class="-left-[100vw]"
        enter-to-class="left-0"
        leave-from-class="left-0"
        leave-to-class="-left-[100vw]"
    >
        <nav
            class="w-[21.875rem] not-xl:fixed not-xl:bg-background not-xl:p-3 not-xl:h-dvh max-w-[80vw] overflow-y-auto flex flex-col z-20 transition-[left] duration-500"
            v-if="mobileOpen || !isMobile"
        >
            <Button
                variant="ghost"
                size="icon"
                @click="mobileOpen = false"
                class="xl:hidden"
                :title="t('closeMenu')"
                :aria-label="t('closeMenu')"
            >
                <LucideMenu aria-hidden="true" />
            </Button>
            <slot name="header" />
            <ul class="mt-3 space-y-1">
                <slot name="navigation-top" />
            </ul>
            <div class="flex-1" />
            <ul class="space-y-1">
                <slot name="navigation-bottom" />
            </ul>
            <footer class="text-muted-foreground text-sm px-3 py-2">
                <slot name="footer" />
            </footer>
        </nav>
    </Transition>
</template>
