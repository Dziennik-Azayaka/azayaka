<script setup lang="ts">
import { useI18n } from "vue-i18n";

import PanelHeader from "@/components/PanelHeader.vue";

const { t } = useI18n();
</script>

<template>
    <div>
        <PanelHeader :breadcrumb-path="[{ href: { name: 'classes' }, title: t('classes') }]" />
        <h1 class="text-2xl font-semibold">{{ t("classes") }}</h1>
        <p class="text-foreground/70 mb-4 text-sm">{{ t("classesDescription") }}</p>
    </div>
</template>
