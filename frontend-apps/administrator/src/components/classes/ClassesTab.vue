<script setup lang="ts">
import { Button } from "#ui/components/ui/button";
import { useQuery } from "@tanstack/vue-query";
import { LucideAlertCircle, LucideLoaderCircle, LucideRefreshCw } from "lucide-vue-next";
import { useI18n } from "vue-i18n";

import type { SchoolUnitEntity } from "@/api/entities/school-structure.ts";
import ClassService from "@/api/services/class.ts";
import ClassesTable from "@/components/classes/ClassesTable.vue";

const props = defineProps<{ tab: "future" | "current" | "archive"; unitsPromise: Promise<SchoolUnitEntity[]> }>();
const emit = defineEmits(["unitsLoadFailed"]);
const { t } = useI18n();

function getUnitShorts(units: SchoolUnitEntity[]) {
    const shorts = new Map<number, string>();
    units.forEach((unit) => shorts.set(unit.id, unit.shortName));
    return shorts;
}

const { data, fetchStatus, isError, refetch } = useQuery({
    queryKey: ["classes", props.tab],
    queryFn: async () => {
        const [schoolUnits, classes] = await Promise.all([
            props.unitsPromise.catch((reason) => {
                emit("unitsLoadFailed");
                throw reason;
            }),
            ClassService.getAllClasses(props.tab),
        ]);
		return { schoolUnits, classes }
    },
});
</script>

<template>
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

    <ClassesTable
        :classes="data.classes"
        :unit-shorts="getUnitShorts(data.schoolUnits)"
        v-else-if="data"
        :show-current-level="tab === 'current'"
    />
</template>
