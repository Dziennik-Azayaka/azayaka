<script setup lang="ts">
import {
  classificationPeriodsFormSchema,
  type ClassificationPeriodsValues,
} from '../../forms/classificationPeriods';
import ClassificationPeriodsChangeRow from './ClassificationPeriodsChangeRow.vue';
import { ErrorBanner } from '@/components/ui/banner';
import {
  FormControl,
  FormField,
  FormFieldArray,
  FormItem,
  FormLabel,
  FormMessage,
} from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import { Separator } from '@/components/ui/separator';
import { CalendarDate } from '@internationalized/date';
import { configure, useForm } from 'vee-validate';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps<{
  schoolYear: number;
  isPending: boolean;
  initialValues?: ClassificationPeriodsValues;
  error: Error | null;
}>();
const emit = defineEmits<{ submit: [ClassificationPeriodsValues] }>();

const { t } = useI18n();
configure({ validateOnInput: true });

const schoolYearRange = computed(() => ({
  start: new CalendarDate(props.schoolYear, 9, 1),
  end: new CalendarDate(props.schoolYear + 1, 8, 31),
}));

const form = useForm({
  validationSchema: classificationPeriodsFormSchema,
  initialValues: props.initialValues ?? {
    periodsNumber: 2,
    periods: [
      { start: schoolYearRange.value.start },
      { start: undefined as unknown as CalendarDate },
    ],
  },
});

function onPeriodsNumberChange() {
  const targetCount = form.values.periodsNumber;
  const currentPeriods = form.values.periods;

  if (!currentPeriods || !targetCount || targetCount < 2 || targetCount > 4) return;
  if (currentPeriods.length === targetCount) return;

  if (currentPeriods.length > targetCount) {
    form.setFieldValue('periods', currentPeriods.slice(0, targetCount));
  } else {
    const diff = targetCount - currentPeriods.length;
    const newElements = Array.from<{ start: CalendarDate }>({ length: diff }).fill({
      start: undefined as unknown as CalendarDate,
    });
    form.setFieldValue('periods', [...currentPeriods, ...newElements]);
    form.setFieldValue('periods[0].start', schoolYearRange.value.start as unknown as never);
  }
}

function minValue(periodIndex: number): CalendarDate | undefined {
  if (!periodIndex) return;
  const previousPeriodStart =
    form.values.periods?.[periodIndex - 1]?.start ?? minValue(periodIndex - 1);
  return previousPeriodStart?.add({ days: 1 });
}

function maxValue(periodIndex: number): CalendarDate | undefined {
  if (!periodIndex || !form.values.periods) return;
  if (periodIndex === form.values.periods.length - 1)
    return schoolYearRange.value.end.subtract({ days: 1 });
  const lastFilled = form.values.periods.find(
    (period, index) => period.start && index > periodIndex,
  );
  return (lastFilled?.start ?? maxValue(periodIndex + 1))?.subtract({ days: 1 });
}

const onSubmit = form.handleSubmit((values) => emit('submit', values));
</script>

<template>
  <form class="space-y-3" @submit="onSubmit">
    <div class="grid grid-cols-1 md:grid-cols-[max-content_1fr] gap-x-5 gap-y-3 items-center">
      <FormField name="periodsNumber" v-slot="{ componentField }">
        <FormItem class="contents">
          <FormLabel>{{ t('administrator.classificationPeriods.periodsNumber') }}</FormLabel>
          <FormControl>
            <Input
              type="number"
              v-bind="componentField"
              @input="onPeriodsNumberChange"
              :disabled="isPending"
              :max="4"
              :min="2"
            />
          </FormControl>
          <FormMessage class="md:col-start-2 md:col-end-3" />
        </FormItem>
      </FormField>
      <Separator class="md:col-start-1 md:col-end-3" />
      <FormFieldArray name="periods" v-slot="{ fields }">
        <ClassificationPeriodsChangeRow
          v-for="(field, index) in fields"
          :field="field"
          :key="field.key"
          :index="index"
          :min="minValue(index)"
          :max="maxValue(index)"
          :disabled="!index || isPending"
        />
      </FormFieldArray>
    </div>

    <ErrorBanner :error="error" v-if="error" />

    <slot />
  </form>
</template>
