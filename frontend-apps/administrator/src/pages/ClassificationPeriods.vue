<script setup lang="ts">
import { useQuery } from "@tanstack/vue-query";
import {
    LucideAlertCircle,
    LucideChevronLeft,
    LucideChevronRight,
    LucideLoaderCircle,
    LucideRefreshCw,
} from "lucide-vue-next";
import { ref } from "vue";
import { useI18n } from "vue-i18n";

import { Button } from "@azayaka-frontend/ui";

import ClassificationPeriodService from "@/api/services/classification-period";
import SchoolStructureService from "@/api/services/school-structure";
import PanelHeader from "@/components/PanelHeader.vue";
import ClassificationPeriodsChangeDialog from "@/components/classification-periods/ClassificationPeriodsChangeDialog.vue";
import ClassificationPeriodsTable from "@/components/classification-periods/ClassificationPeriodsTable.vue";
import { getCurrentSchoolYear, getSchoolYearString } from "@/utils";

const { t } = useI18n();

const schoolYear = ref(getCurrentSchoolYear());

async function load() {
    const schoolUnits = (await SchoolStructureService.getSchoolUnits()).filter((unit) => !unit.archived);
    const classificationPeriods = new Map(
        await Promise.all(
            schoolUnits.map(
                async (unit) =>
                    [
                        unit.id,
                        await ClassificationPeriodService.getClassificationPeriodsByUnitId(unit.id, schoolYear.value),
                    ] as const,
            ),
        ),
    );

    return { schoolUnits, classificationPeriods };
}

const { data, isError, refetch, fetchStatus } = useQuery({
    queryKey: ["classificationPeriods", schoolYear],
    queryFn: load,
});
</script>

<template>
    <div>
        <PanelHeader
            :breadcrumb-path="[{ href: { name: 'classificationPeriods' }, title: t('classificationPeriods') }]"
        />
        <h1 class="text-2xl font-semibold">{{ t("classificationPeriods") }}</h1>
        <p class="text-foreground/70 mb-4 text-sm">{{ t("classificationPeriodsDescription") }}</p>

        <section class="px-3 sm:px-5 py-3 gap-2 border rounded-md flex justify-between items-center">
            <p class="font-semibold">
                <span class="not-md:hidden">Rok szkolny</span> {{ getSchoolYearString(schoolYear) }}
            </p>
            <div class="flex gap-3">
                <ClassificationPeriodsChangeDialog
                    v-if="data && data.schoolUnits.length === 1"
                    :unit="data.schoolUnits[0]"
                    :periods="data.classificationPeriods.get(data.schoolUnits[0].id)!!"
                    :show-unit-name="false"
                    :school-year="schoolYear"
                />
                <Button variant="ghost" size="icon" @click="schoolYear--" :disabled="schoolYear <= 2000">
                    <LucideChevronLeft />
                </Button>
                <Button variant="ghost" size="icon" @click="schoolYear++" :disabled="schoolYear >= 2100">
                    <LucideChevronRight />
                </Button>
            </div>
        </section>

        <div class="min-h-96 content-center" v-if="fetchStatus === 'fetching'">
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

        <template v-else-if="data">
            <ClassificationPeriodsTable
                v-for="unit in data.schoolUnits"
                :key="unit.id"
                :unit="unit"
                :show-header="data.schoolUnits.length !== 1"
                :periods="data.classificationPeriods.get(unit.id)!!"
            />
        </template>
    </div>
</template>
