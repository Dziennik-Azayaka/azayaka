<script lang="ts" setup>
import { useI18n } from "vue-i18n";

import { AccessStatus } from "@/api/entities/access.ts";

defineProps<{ status: AccessStatus }>();

const { t } = useI18n();

const statusClasses: Record<AccessStatus, string> = {
    unactive:
        "bg-yellow-700/10 text-yellow-800 border-yellow-700/60 dark:bg-yellow-300/10 dark:text-yellow-300 dark:border-yellow-300/60",
    codeGenerated:
        "bg-blue-700/10 text-blue-800 border-blue-700/60 dark:bg-blue-300/10 dark:text-blue-300 dark:border-blue-300/60",
    active: "bg-green-700/10 text-green-800 border-green-700/60 dark:bg-green-300/10 dark:text-green-300 dark:border-green-300/60",
};
</script>

<template>
    <div :class="statusClasses[status]" class="px-1.5 py-1 border rounded-md text-xs employee-role inline">
        {{ t(`accessStatus.${status.toString()}`) }}
    </div>
</template>
