<script setup lang="ts">
import { useQuery } from "@tanstack/vue-query";
import { LucideAlertCircle, LucideLoaderCircle, LucideRefreshCw } from "lucide-vue-next";
import { ref } from "vue";
import { useI18n } from "vue-i18n";

import { Button } from "@azayaka-frontend/ui";

import SchoolStructureService from "@/api/services/school-structure";
import PanelHeader from "@/components/PanelHeader.vue";
import AddSchoolUnitComplexRequiredDialog from "@/components/school-structure/AddSchoolUnitComplexRequiredDialog.vue";
import SchoolComplexCard from "@/components/school-structure/SchoolComplexCard.vue";
import SchoolComplexCreateDialog from "@/components/school-structure/SchoolComplexCreateDialog.vue";
import SchoolUnitCard from "@/components/school-structure/SchoolUnitCard.vue";

const { t } = useI18n();
const {
    data: structure,
    isLoading,
    isError,
    refetch,
} = useQuery({ queryKey: ["schoolStructure"], queryFn: SchoolStructureService.getStructure });

const showComplexCreateDialog = ref(false);
</script>

<template>
    <div>
        <PanelHeader :breadcrumb-path="[{ href: { name: 'schoolStructure' }, title: t('schoolStructure') }]" />
        <h1 class="text-2xl font-semibold">{{ t("schoolStructure") }}</h1>
        <p class="text-foreground/70 mb-4 text-sm">{{ t("schoolStructureDescription") }}</p>

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

        <template v-else-if="structure">
            <div class="flex justify-end mb-4" v-if="structure.mode === 'single'">
                <AddSchoolUnitComplexRequiredDialog @continue="showComplexCreateDialog = true" />
                <SchoolComplexCreateDialog v-model="showComplexCreateDialog" @created="refetch" />
            </div>
            <ul class="space-y-6">
                <SchoolUnitCard v-if="structure.mode === 'single'" :data="structure.unit" @edited="refetch" />
                <SchoolComplexCard :data="structure.complex" @structure-changed="refetch" v-else />
            </ul>
        </template>
    </div>
</template>
