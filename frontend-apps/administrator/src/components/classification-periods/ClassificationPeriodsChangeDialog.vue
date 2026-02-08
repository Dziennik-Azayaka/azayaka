<script setup lang="ts">
import ClassificationPeriodsChangeForm from "./ClassificationPeriodsChangeForm.vue";
import { LucidePencil } from "lucide-vue-next";
import { ref } from "vue";
import { useI18n } from "vue-i18n";

import {
    Button,
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from "@azayaka-frontend/ui";

import type { ClassificationPeriodEntity } from "@/api/entities/classification-period";
import type { SchoolUnitEntity } from "@/api/entities/school-structure";
import { getSchoolYearString } from "@/utils";

defineProps<{
    unit: SchoolUnitEntity;
    showUnitName: boolean;
    periods: ClassificationPeriodEntity[];
    schoolYear: number;
}>();

const { t } = useI18n();
const showDialog = ref(false);

function onSubmit(starts: Date[]) {
    console.log(starts);
}
</script>

<template>
    <Dialog v-model:open="showDialog">
        <DialogTrigger>
            <Button variant="secondary">
                <LucidePencil />
                {{ t("periodsChange") }}
            </Button>
        </DialogTrigger>
        <DialogContent>
            <DialogHeader>
                <DialogTitle>{{ t("changePeriods", { schoolYear: getSchoolYearString(schoolYear) }) }}</DialogTitle>
                <DialogDescription v-if="showUnitName">{{ unit.name }}</DialogDescription>
            </DialogHeader>
            <ClassificationPeriodsChangeForm :school-year="schoolYear" :periods="periods" @submit="onSubmit">
                <template #footer>
                    <DialogClose as-child>
                        <Button variant="outline">{{ t("cancel") }}</Button>
                    </DialogClose>
                    <Button>{{ t("save") }}</Button>
                </template>
            </ClassificationPeriodsChangeForm>
        </DialogContent>
    </Dialog>
</template>
