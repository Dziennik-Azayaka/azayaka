<script setup lang="ts">
import { Button } from "#ui/components/ui/button";
import { LucideAlertCircle, LucideLoaderCircle, LucideRefreshCw } from "lucide-vue-next";
import { onMounted, ref } from "vue";
import { useI18n } from "vue-i18n";

import type { ClassEntity } from "@/api/entities/class.ts";
import type { SchoolUnitEntity } from "@/api/entities/school-structure.ts";
import ClassService from "@/api/services/class.ts";
import ClassesTable from "@/components/classes/ClassesTable.vue";

const props = defineProps<{ tab: "future" | "current" | "archive"; unitsPromise: Promise<SchoolUnitEntity[]> }>();
const emit = defineEmits(["unitsLoadFailed"]);
const { t } = useI18n();

const loading = ref(true);
const error = ref(false);
const schoolUnits = ref<SchoolUnitEntity[] | null>(null);
const classes = ref<ClassEntity[] | null>(null);

function getUnitShorts(units: SchoolUnitEntity[]) {
    const shorts = new Map<number, string>();
    units.forEach((unit) => shorts.set(unit.id, unit.shortName));
    return shorts;
}

async function load() {
    loading.value = true;
    schoolUnits.value = null;
    classes.value = null;
    try {
        [schoolUnits.value, classes.value] = await Promise.all([
            props.unitsPromise.catch((reason) => {
                emit("unitsLoadFailed");
                throw reason;
            }),
            ClassService.getAllClasses(props.tab),
        ]);
    } catch {
        error.value = true;
    } finally {
        loading.value = false;
    }
}

onMounted(load);
</script>

<template>
    <div class="min-h-96 content-center" v-if="loading">
        <LucideLoaderCircle class="animate-spin mx-auto" :aria-label="t('pleaseWait')" />
    </div>

    <div class="min-h-96 flex flex-col items-center justify-center" v-else-if="error">
        <LucideAlertCircle class="mx-auto size-9" />
        <p class="text-center mt-2 font-medium">{{ t("unknownError") }}</p>
        <Button class="mx-auto mt-3" variant="outline" @click="load">
            <LucideRefreshCw />
            {{ t("tryAgain") }}
        </Button>
    </div>

    <ClassesTable
        :classes="classes"
        :unit-shorts="getUnitShorts(schoolUnits)"
        v-else-if="classes && schoolUnits"
        :show-current-level="tab === 'current'"
    />
</template>
