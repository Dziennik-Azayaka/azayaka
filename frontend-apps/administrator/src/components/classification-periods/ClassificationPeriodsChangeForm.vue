<script setup lang="ts">
import ClassificationPeriodsChangeRow from "./ClassificationPeriodsChangeRow.vue";
import { CalendarDate, fromDate, getLocalTimeZone, toCalendarDate } from "@internationalized/date";
import { toTypedSchema } from "@vee-validate/zod";
import { configure, useForm } from "vee-validate";
import { computed, onMounted } from "vue";
import { useI18n } from "vue-i18n";
import * as z from "zod";

import {
    DialogFooter,
    FormControl,
    FormField,
    FormFieldArray,
    FormItem,
    FormLabel,
    FormMessage,
    Input,
} from "@azayaka-frontend/ui";

import type { ClassificationPeriodEntity } from "@/api/entities/classification-period";

const props = defineProps<{
    periods: ClassificationPeriodEntity[];
    schoolYear: number;
}>();
const emit = defineEmits<{ submit: [periodStarts: Date[]] }>();

const { t } = useI18n();

configure({ validateOnInput: true });

const formSchema = toTypedSchema(
    z.object({
        periodsNumber: z
            .number({ required_error: t("requiredFieldError"), invalid_type_error: t("requiredFieldError") })
            .min(2, t("incorrectPeriodsNumberError"))
            .max(4, t("incorrectPeriodsNumberError")),
        periods: z.array(z.object({ start: z.instanceof(CalendarDate, { message: t("requiredFieldError") }) })),
    }),
);

const form = useForm({
    validationSchema: formSchema,
    initialValues: {
        periodsNumber: 2,
        periods: [{ start: null as unknown as CalendarDate }, { start: null as unknown as CalendarDate }],
    },
});

const schoolYearRange = computed(() => ({
    start: new CalendarDate(props.schoolYear, 9, 1),
    end: new CalendarDate(props.schoolYear + 1, 8, 31),
}));

function onPeriodsNumberChange() {
    const targetCount = form.values.periodsNumber;
    const currentPeriods = form.values.periods;

    if (!currentPeriods || !targetCount || targetCount < 2 || targetCount > 4) return;
    if (currentPeriods.length === targetCount) return;

    if (currentPeriods.length > targetCount) {
        form.setFieldValue("periods", currentPeriods.slice(0, targetCount));
    } else {
        const diff = targetCount - currentPeriods.length;
        const newElements = new Array(diff).fill({ start: undefined });
        form.setFieldValue("periods", [...currentPeriods, ...newElements]);
        form.setFieldValue("periods[0].start", schoolYearRange.value.start as unknown as never);
    }
}

function minValue(periodIndex: number): CalendarDate | undefined {
    if (!periodIndex) return;
    const previousPeriodStart = form.values.periods?.[periodIndex - 1].start ?? minValue(periodIndex - 1);
    return previousPeriodStart?.add({ days: 1 });
}

function maxValue(periodIndex: number): CalendarDate | undefined {
    if (!periodIndex || !form.values.periods) return;
    if (periodIndex === form.values.periods.length - 1) return schoolYearRange.value.end.subtract({ days: 1 });
    const lastFilled = form.values.periods.find((period, index) => period.start && index > periodIndex);
    return (lastFilled?.start ?? maxValue(periodIndex + 1))?.subtract({ days: 1 });
}

const onSubmit = form.handleSubmit((values) => {
    const starts = values.periods.map((period) => period.start?.toDate(getLocalTimeZone()));
    emit("submit", starts);
});

onMounted(() => {
    if (!props.periods.length) {
        onPeriodsNumberChange();
        form.setFieldValue("periods[0].start", schoolYearRange.value.start as never);
    } else {
        form.setFieldValue("periodsNumber", props.periods.length);
        form.setFieldValue(
            "periods",
            props.periods.map((period) => ({ start: toCalendarDate(fromDate(period.start, getLocalTimeZone())) })),
        );
    }
});
</script>

<template>
    <form class="space-y-3" @submit="onSubmit">
        <div class="grid grid-cols-1 md:grid-cols-[max-content_1fr] gap-x-5 gap-y-3 items-center">
            <FormField name="periodsNumber" v-slot="{ componentField }">
                <FormItem class="contents">
                    <FormLabel>{{ t("periodsNumber") }}</FormLabel>
                    <FormControl>
                        <Input type="number" v-bind="componentField" @input="onPeriodsNumberChange" />
                    </FormControl>
                    <FormMessage class="md:col-start-2 md:col-end-3" />
                </FormItem>
            </FormField>
            <hr class="md:col-start-1 md:col-end-3" />
            <FormFieldArray name="periods" v-slot="{ fields }">
                <ClassificationPeriodsChangeRow
                    v-for="(field, index) in fields"
                    :field="field"
                    :key="field.key"
                    :index="index"
                    :min="minValue(index)"
                    :max="maxValue(index)"
                />
            </FormFieldArray>
        </div>
        <DialogFooter>
            <slot name="footer" />
        </DialogFooter>
    </form>
</template>
