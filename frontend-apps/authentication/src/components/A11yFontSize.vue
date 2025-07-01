<script setup lang="ts">
import { useI18n } from "vue-i18n";

import { useMainStore } from "@/stores/main.store";

const mainStore = useMainStore();
const { t } = useI18n();
</script>

<template>
    <fieldset class="flex items-end gap-1.5" aria-hidden="true">
        <legend class="sr-only">{{ t("fontSize") }}</legend>
        <label
            class="rounded-sm text-[1.75rem]/10 cursor-pointer transition-colors"
            :class="{
                'bg-primary text-primary-foreground': mainStore.fontSize === 'large',
            }"
        >
            <span class="p-1">A</span>
            <input type="radio" name="font-size" value="large" class="sr-only" v-model="mainStore.fontSize" />
        </label>
        <label
            class="rounded-sm text-[1.25rem]/8 cursor-pointer transition-colors"
            :class="{
                'bg-primary text-primary-foreground': mainStore.fontSize === 'normal',
            }"
        >
            <span class="p-1">A</span>
            <input type="radio" name="font-size" value="normal" class="sr-only" v-model="mainStore.fontSize" />
        </label>
    </fieldset>
</template>
