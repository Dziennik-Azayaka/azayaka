<script setup lang="ts">
import ClassificationPeriodsChangeForm from "./ClassificationPeriodsChangeForm.vue";
import { type CalendarDate } from "@internationalized/date";
import { useMutation, useQueryClient } from "@tanstack/vue-query";
import { LucidePencil, LucideLoader2 } from "lucide-vue-next";
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
import ClassificationPeriodService from "@/api/services/classification-period";

const props = defineProps<{
    unit: SchoolUnitEntity;
    showUnitName: boolean;
    periods: ClassificationPeriodEntity[];
    schoolYear: number;
}>();

const { t } = useI18n();
const queryClient = useQueryClient();

const showDialog = ref(false);

const { mutate: onSubmit, isPending, isError } = useMutation({
    mutationKey: ["classificationPeriods", props.unit.id, props.schoolYear],
    mutationFn: async (starts: CalendarDate[]) => {
        const ends = starts.map((_, index) => starts[index].subtract({ days: 1 }).toString()).sort().slice(1);
		await ClassificationPeriodService.changeClassificationPeriodsForUnit(props.unit.id, props.schoolYear, ends);
		await queryClient.invalidateQueries({ queryKey: ["classificationPeriods", props.schoolYear] });
		showDialog.value = false;
    },
});
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
            <ClassificationPeriodsChangeForm :school-year="schoolYear" :periods="periods" @submit="onSubmit" :loading="isPending" :error="isError">
                <template #footer>
                    <DialogClose as-child>
                        <Button variant="outline">{{ t("cancel") }}</Button>
                    </DialogClose>
                    <Button>
						<template v-if="!isPending">{{ t("save") }}</template>
						<LucideLoader2 v-else class="animate-spin size-5" :aria-label="t('pleaseWait')" />
					</Button>
                </template>
            </ClassificationPeriodsChangeForm>
        </DialogContent>
    </Dialog>
</template>
