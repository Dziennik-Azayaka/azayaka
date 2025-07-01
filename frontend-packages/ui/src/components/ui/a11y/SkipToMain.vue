<script setup lang="ts">
import { useI18n } from "vue-i18n";

const { t } = useI18n();
</script>

<template>
    <nav>
        <a
            class="not-focus:size-0 overflow-hidden focus:px-5 focus:py-3 fixed z-50 top-5 left-5 bg-background rounded-md font-medium text-lg"
            href="#main-content"
        >
            {{ t("skipToContent") }}
        </a>
    </nav>
</template>
