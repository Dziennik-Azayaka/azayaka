<script setup lang="ts">
import { Button } from "#ui/components/ui/button";
import { useQuery } from "@tanstack/vue-query";
import { LucideAlertCircle, LucideLoaderCircle, LucideRefreshCw } from "lucide-vue-next";
import { useI18n } from "vue-i18n";

import AccessService from "@/api/services/access";
import EmployeeAccessesTable from "@/components/system-access/EmployeeAccessesTable.vue";

const { t } = useI18n();

const {
    data: accesses,
    isLoading,
    isError,
    refetch,
} = useQuery({ queryKey: ["accesses", "employee"], queryFn: AccessService.getEmployeeAccesses });
</script>

<template>
    <div class="min-h-96 content-center" v-if="isLoading">
        <LucideLoaderCircle class="animate-spin mx-auto" :aria-label="t('pleaseWait')" />
    </div>

    <div class="min-h-96 flex flex-col items-center justify-center" v-else-if="isError">
        <LucideAlertCircle class="mx-auto size-9" />
        <p class="text-center mt-2 font-medium">{{ t("unknownError") }}</p>
        <Button class="mx-auto mt-3" variant="outline" @click="refetch">
            <LucideRefreshCw />
            {{ t("tryAgain") }}
        </Button>
    </div>

    <template v-else-if="accesses">
        <EmployeeAccessesTable :accesses="accesses" @refresh-needed="refetch" />
    </template>
</template>
