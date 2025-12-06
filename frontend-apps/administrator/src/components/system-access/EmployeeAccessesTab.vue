<script setup lang="ts">
import { Button } from "#ui/components/ui/button";
import { LucideAlertCircle, LucideLoaderCircle, LucideRefreshCw } from "lucide-vue-next";
import { onMounted, ref } from "vue";
import { useI18n } from "vue-i18n";

import type { EmployeeAccessEntity } from "@/api/entities/employee-access.ts";
import AccessService from "@/api/services/access";
import EmployeeAccessesTable from "@/components/system-access/EmployeeAccessesTable.vue";

const loading = ref(true);
const error = ref(false);
const accesses = ref<EmployeeAccessEntity[] | null>();
const { t } = useI18n();

async function getEmployeeAccesses() {
    loading.value = true;
    error.value = false;
    accesses.value = [];
    try {
        accesses.value = await AccessService.getEmployeeAccesses();
    } catch {
        error.value = true;
    } finally {
        loading.value = false;
    }
}

onMounted(getEmployeeAccesses);
</script>

<template>
    <div class="min-h-96 content-center" v-if="loading">
        <LucideLoaderCircle class="animate-spin mx-auto" :aria-label="t('pleaseWait')" />
    </div>

    <div class="min-h-96 flex flex-col items-center justify-center" v-else-if="error">
        <LucideAlertCircle class="mx-auto size-9" />
        <p class="text-center mt-2 font-medium">{{ t("unknownError") }}</p>
        <Button class="mx-auto mt-3" variant="outline" @click="getEmployeeAccesses">
            <LucideRefreshCw />
            {{ t("tryAgain") }}
        </Button>
    </div>

    <template v-else-if="accesses">
        <EmployeeAccessesTable :accesses="accesses" />
    </template>
</template>
