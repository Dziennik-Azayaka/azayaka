<script setup lang="ts">
import { useI18n } from "vue-i18n";

import PanelHeader from "@/components/PanelHeader.vue";

const { t } = useI18n();
</script>

<template>
    <div>
        <PanelHeader :breadcrumb-path="[{ href: { name: 'systemAccess' }, title: t('systemAccess') }]" />
        <h1 class="text-2xl font-semibold">{{ t("systemAccess") }}</h1>
        <p class="text-foreground/70 mb-4 text-sm">{{ t("systemAccessDescription") }}</p>
    </div>
</template>
