<script setup lang="ts">
import { LucideLoaderCircle, LucideRefreshCw } from "lucide-vue-next";
import { onMounted, ref } from "vue";
import { useI18n } from "vue-i18n";

import { Button } from "@azayaka-frontend/ui";

import type { SchoolStructureEntity } from "@/api/entities/school-structure";
import SchoolStructureService from "@/api/services/school-structure";
import PanelHeader from "@/components/PanelHeader.vue";
import AddSchoolUnitComplexRequiredDialog from "@/components/school-structure/AddSchoolUnitComplexRequiredDialog.vue";
import SchoolComplexCard from "@/components/school-structure/SchoolComplexCard.vue";
import SchoolComplexCreateDialog from "@/components/school-structure/SchoolComplexCreateDialog.vue";
import SchoolUnitCard from "@/components/school-structure/SchoolUnitCard.vue";

const { t } = useI18n();

const loading = ref(true);
const error = ref(false);
const structure = ref<SchoolStructureEntity | null>();

const showComplexCreateDialog = ref(false);

async function getStructure() {
    loading.value = true;
    error.value = false;
    try {
        structure.value = await SchoolStructureService.getStructure();
    } catch {
        error.value = true;
    } finally {
        loading.value = false;
    }
}

onMounted(getStructure);
</script>

<template>
    <div>
        <PanelHeader :breadcrumb-path="[{ href: { name: 'schoolStructure' }, title: t('schoolStructure') }]" />
        <h1 class="text-2xl font-semibold">{{ t("schoolStructure") }}</h1>
        <p class="text-foreground/70 mb-4 text-sm">{{ t("schoolStructureDescription") }}</p>

        <div class="h-96 content-center" v-if="loading">
            <LucideLoaderCircle class="animate-spin mx-auto" :aria-label="t('pleaseWait')" />
        </div>

        <div class="h-96 flex flex-col items-center justify-center" v-else-if="error">
            <LucideAlertCircle class="mx-auto size-9" />
            <p class="text-center mt-2 font-medium">{{ t("unknownError") }}</p>
            <Button class="mx-auto mt-3" variant="outline" @click="getStructure">
                <LucideRefreshCw />
                {{ t("tryAgain") }}
            </Button>
        </div>

        <template v-else-if="structure">
            <div class="flex justify-end mb-4" v-if="structure.mode === 'single'">
                <AddSchoolUnitComplexRequiredDialog
                    @continue="
                        (() => {
                            showComplexCreateDialog = true;
                        })()
                    "
                />
                <SchoolComplexCreateDialog v-model="showComplexCreateDialog" @created="getStructure()" />
            </div>
            <ul class="space-y-6">
                <SchoolUnitCard v-if="structure.mode === 'single'" :data="structure.unit" @edited="getStructure()" />
                <SchoolComplexCard :data="structure.complex" @structure-changed="getStructure()" v-else />
            </ul>
        </template>
    </div>
</template>
