<script lang="ts" setup>
import { useI18n } from "vue-i18n";

import type { EmployeeRole } from "@/api/entities/employee";

defineProps<{ roles: Set<EmployeeRole> }>();

const { t } = useI18n();

const roleClasses: Record<EmployeeRole, string> = {
    administrator:
        "bg-yellow-700/10 text-yellow-800 border-yellow-700/60 dark:bg-yellow-300/10 dark:text-yellow-300 dark:border-yellow-300/60",
    teacher:
        "bg-blue-700/10 text-blue-800 border-blue-700/60 dark:bg-blue-300/10 dark:text-blue-300 dark:border-blue-300/60",
    secretary:
        "bg-green-700/10 text-green-800 border-green-700/60 dark:bg-green-300/10 dark:text-green-300 dark:border-green-300/60",
    headmaster:
        "bg-violet-700/10 text-violet-800 border-violet-700/60 dark:bg-violet-300/10 dark:text-violet-300 dark:border-violet-300/60",
};
</script>

<template>
    <ul class="flex gap-3">
        <li
            v-for="role in roles"
            :key="role"
            :class="roleClasses[role]"
            class="px-1.5 py-1 border rounded-md text-xs employee-role"
        >
            {{ t(`role.${role}`) }}
        </li>
    </ul>
</template>
