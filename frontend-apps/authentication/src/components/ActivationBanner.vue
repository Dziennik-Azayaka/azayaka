<script setup lang="ts">
import { LucideChevronRight } from "lucide-vue-next";
import { useI18n } from "vue-i18n";

import { Button } from "@azayaka-frontend/ui";

const { t } = useI18n();
</script>

<template>
    <div
        role="alert"
        class="px-6 py-5 flex not-sm:flex-col justify-between items-center gap-3 bg-foreground dark:bg-accent text-background dark:text-foreground rounded-lg"
    >
        <p class="text-sm font-medium">
            {{ t("activationBannerText") }}
        </p>
        <Button
            size="sm"
            variant="outline"
            class="!bg-background dark:!bg-primary text-foreground dark:text-background not-sm:w-full focus-visible:outline-background"
            as-child
        >
            <RouterLink :to="{ name: 'activation.code' }">
                {{ t("activationBannerButton") }}
                <LucideChevronRight />
            </RouterLink>
        </Button>
    </div>
</template>
