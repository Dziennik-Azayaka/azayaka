<script setup lang="ts">
import type { ClassificationPeriodsValues } from '../../forms/classificationPeriods';
import ClassificationPeriodsChangeForm from './ClassificationPeriodsChangeForm.vue';
import { useSetClassificationPeriods } from '@/api/hooks/classification-period/setClassificationPeriods';
import type { ClassificationPeriod } from '@/api/types/classification-period';
import type { SchoolUnit } from '@/api/types/school-structure';
import { Button } from '@/components/ui/button';
import {
  Dialog,
  DialogClose,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from '@/components/ui/dialog';
import { schoolYearString } from '@/lib/utils';
import { CalendarDate } from '@internationalized/date';
import { LucidePencil } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps<{
  unit: SchoolUnit;
  showUnitName: boolean;
  periods: ClassificationPeriod[];
  schoolYear: number;
}>();

const { t } = useI18n();

const showDialog = ref(false);

const { mutate: set, isPending, error } = useSetClassificationPeriods();

async function onSubmit(values: ClassificationPeriodsValues) {
  const ends = values.periods.slice(1).map(({ start }) => start.add({ days: -1 }).toString());
  await set({ unitId: props.unit.id, schoolYear: props.schoolYear, ends });
}

const formInitialValues = computed(() => {
  if (!props.periods.length) return;

  const periodsNumber = props.periods.length;
  const periods = props.periods.map((period) => {
    const start = period.start;
    return { start: new CalendarDate(start.getFullYear(), start.getMonth() + 1, start.getDay()) };
  });

  return { periodsNumber, periods };
});
</script>

<template>
  <Dialog v-model:open="showDialog">
    <DialogTrigger>
      <Button variant="secondary">
        <LucidePencil aria-hidden="true" />
        {{ t('administrator.classificationPeriods.changePeriods') }}
      </Button>
    </DialogTrigger>
    <DialogContent>
      <DialogHeader>
        <DialogTitle>
          {{
            t('administrator.classificationPeriods.changePeriodsYear', {
              schoolYear: schoolYearString(schoolYear),
            })
          }}
        </DialogTitle>
        <DialogDescription v-if="showUnitName">{{ unit.name }}</DialogDescription>
      </DialogHeader>
      <ClassificationPeriodsChangeForm
        :school-year="schoolYear"
        :periods="periods"
        @submit="onSubmit"
        :is-pending="isPending"
        :error="error"
        :initial-values="formInitialValues"
      >
        <DialogFooter>
          <DialogClose as-child>
            <Button variant="outline">{{ t('common.actions.cancel') }}</Button>
          </DialogClose>
          <Button :loading="isPending">
            {{ t('common.actions.save') }}
          </Button>
        </DialogFooter>
      </ClassificationPeriodsChangeForm>
    </DialogContent>
  </Dialog>
</template>
