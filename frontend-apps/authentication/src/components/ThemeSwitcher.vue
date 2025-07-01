<script setup lang="ts">
import { LucideContrast } from "lucide-vue-next";
import { useI18n } from "vue-i18n";

import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuLabel,
    DropdownMenuRadioGroup,
    DropdownMenuRadioItem,
    DropdownMenuTrigger,
} from "@azayaka-frontend/ui";

import { useMainStore } from "@/stores/main.store";

const mainStore = useMainStore();
const { t } = useI18n();
</script>

<template>
    <DropdownMenu>
        <DropdownMenuTrigger aria-hidden="true" :title="t('changeTheme')" class="cursor-pointer">
            <LucideContrast />
        </DropdownMenuTrigger>
        <DropdownMenuContent aria-hidden="true">
            <DropdownMenuLabel>{{ t("changeTheme") }}</DropdownMenuLabel>
            <DropdownMenuRadioGroup v-model="mainStore.colorMode">
                <DropdownMenuRadioItem value="light">{{ t("light") }}</DropdownMenuRadioItem>
                <DropdownMenuRadioItem value="dark">{{ t("dark") }}</DropdownMenuRadioItem>
                <DropdownMenuRadioItem value="high-contrast">
                    {{ t("highContrast") }}
                </DropdownMenuRadioItem>
            </DropdownMenuRadioGroup>
        </DropdownMenuContent>
    </DropdownMenu>
</template>
