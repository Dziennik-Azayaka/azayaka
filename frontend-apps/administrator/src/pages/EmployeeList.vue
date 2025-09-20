<script setup lang="ts">
import { Button } from "#ui/components/ui/button";
import { LucideAlertCircle, LucideLoaderCircle, LucideRefreshCw } from "lucide-vue-next";
import { onMounted, ref } from "vue";
import { useI18n } from "vue-i18n";

import type { Employee } from "@/api/entities/employee";
import EmployeeService from "@/api/services/employee";
import PanelHeader from "@/components/PanelHeader.vue";

const { t } = useI18n();

const loading = ref(true);
const error = ref(false);
const employees = ref<Employee[] | null>();

async function getEmployees() {
    loading.value = true;
    error.value = false;
    try {
        employees.value = await EmployeeService.getAllEmployees();
    } catch {
        error.value = true;
    } finally {
        loading.value = false;
    }
}

onMounted(getEmployees);
</script>

<template>
    <div>
        <PanelHeader :breadcrumb-path="[{ href: { name: 'employees' }, title: t('employees') }]" />
        <h1 class="text-2xl font-semibold">{{ t("employees") }}</h1>
        <p class="text-foreground/70 mb-4 text-sm">{{ t("employeesDescription") }}</p>

        <div class="min-h-96 content-center" v-if="loading">
            <LucideLoaderCircle class="animate-spin mx-auto" :aria-label="t('pleaseWait')" />
        </div>

        <div class="min-h-96 flex flex-col items-center justify-center" v-else-if="error">
            <LucideAlertCircle class="mx-auto size-9" />
            <p class="text-center mt-2 font-medium">{{ t("unknownError") }}</p>
            <Button class="mx-auto mt-3" variant="outline" @click="getEmployees">
                <LucideRefreshCw />
                {{ t("tryAgain") }}
            </Button>
        </div>

        <template v-else-if="employees">
            {{ employees }}
        </template>
    </div>
</template>
