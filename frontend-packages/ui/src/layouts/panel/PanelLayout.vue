<script setup lang="ts">
import { A11ySkipToMain } from "#ui/components/ui/a11y";
</script>

<template>
    <A11ySkipToMain />
    <div class="w-screen h-dvh flex gap-3 xl:p-3 items-stretch xl:bg-sidebar">
        <slot name="navigation" />
        <div class="flex-1 rounded-md xl:border bg-background px-6 md:px-8 py-5 space-y-2 overflow-y-auto">
            <slot name="content" />
        </div>
    </div>
</template>
