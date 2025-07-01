<script setup lang="ts">
import { LucideChevronsUpDown } from "lucide-vue-next";

defineProps<{ title: string; subtitle?: string }>();
</script>

<template>
    <button
        class="flex w-full text-start gap-3 items-center px-3 md:px-2.5 py-2 rounded-md hover:bg-accent transition-colors cursor-pointer"
    >
        <div class="flex-1 overflow-hidden">
            <h1 class="font-semibold">{{ title }}</h1>
            <p class="text-xs text-muted-foreground text-ellipsis overflow-hidden text-nowrap" v-if="subtitle">
                {{ subtitle }}
            </p>
        </div>
        <LucideChevronsUpDown aria-label="Przejdź do innego modułu" title="Przejdź do innego modułu" class="size-4" />
    </button>
</template>
