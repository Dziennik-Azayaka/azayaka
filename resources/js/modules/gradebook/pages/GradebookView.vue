<script setup lang="ts">
import { useGetClassUnits } from '@/api/hooks/class-unit/getClassUnits';
import { useGetClassificationPeriodsForUnit } from '@/api/hooks/classification-period/getClassificationPeriodsForUnit';
import { useCreateGradebook } from '@/api/hooks/gradebook/createGradebook';
import { useGetGradebooks } from '@/api/hooks/gradebook/getGradebooks';
import PanelPageHeader from '@/components/panel-layout/PanelPageHeader.vue';
import { ErrorBanner } from '@/components/ui/banner';
import { Button } from '@/components/ui/button';
import { Empty, EmptyContent, EmptyHeader, EmptyMedia, EmptyTitle } from '@/components/ui/empty';
import { EmptyLoading } from '@/components/ui/empty';
import EmptyLoadingError from '@/components/ui/empty/EmptyLoadingError.vue';
import { useGradebookStore } from '@/stores/gradebook';
import { LucideBookOpen, LucidePlus } from 'lucide-vue-next';
import { computed } from 'vue';
import { watch } from 'vue';
import { useI18n } from 'vue-i18n';
import { useRoute } from 'vue-router';

const { t } = useI18n();
const route = useRoute();
const gradebookStore = useGradebookStore();

const classUnitId = computed(() => Number(route.params.classUnitId));
const schoolYear = computed(() => Number(route.params.schoolYear));

const {
  data: classUnits,
  isFetching: classUnitsLoading,
  isError: classUnitsError,
  refetch: refetchClassUnits,
} = useGetClassUnits();

const selectedClassUnit = computed(() =>
  classUnits.value?.find((cu) => cu.id === classUnitId.value),
);

const schoolUnitId = computed(() => selectedClassUnit.value?.schoolUnit.id);

const {
  data: gradebooks,
  isFetching: gradebooksLoading,
  isError: gradebooksError,
  refetch: refetchGradebooks,
} = useGetGradebooks(schoolUnitId, classUnitId, schoolYear);

const existingGradebook = computed(() => gradebooks.value?.[0] ?? null);

const { data: classificationPeriods, isFetching: classificationPeriodsLoading } =
  useGetClassificationPeriodsForUnit(schoolUnitId, schoolYear);

const {
  mutate: createGradebook,
  isPending: createPending,
  error: createError,
} = useCreateGradebook();

const noGradebook = computed(
  () =>
    selectedClassUnit.value &&
    !gradebooksLoading.value &&
    !classUnitsLoading.value &&
    !existingGradebook.value,
);

watch(
  [selectedClassUnit, existingGradebook],
  ([classUnit, gradebook]) => {
    if (classUnit && schoolYear.value) {
      gradebookStore.setSelection(classUnit, schoolYear.value);
      gradebookStore.setGradebook(gradebook ?? null);
    }
  },
  { immediate: true },
);

function handleCreate() {
  if (!classificationPeriods.value?.length) return;
  const periodId = classificationPeriods.value[0]!.id;
  createGradebook(
    { classificationPeriodId: periodId, classUnitId: classUnitId.value },
    {
      onSuccess() {
        refetchGradebooks();
      },
    },
  );
}
</script>

<template>
  <PanelPageHeader :title="t('gradebook.view.title')" />

  <EmptyLoading v-if="classUnitsLoading || gradebooksLoading || classificationPeriodsLoading" />

  <EmptyLoadingError v-else-if="classUnitsError" @refresh="refetchClassUnits" />
  <EmptyLoadingError v-else-if="gradebooksError" @refresh="refetchGradebooks" />

  <template v-else-if="existingGradebook">
    <RouterView />
  </template>

  <template v-else-if="noGradebook">
    <Empty class="border border-dashed border-foreground/30">
      <EmptyHeader>
        <EmptyMedia variant="icon">
          <LucideBookOpen />
        </EmptyMedia>
        <EmptyTitle>{{
          t('gradebook.selector.notCreatedInfo.title', {
            classUnitMark: selectedClassUnit!.mark,
            schoolYear: `${schoolYear}/${schoolYear + 1}`,
          })
        }}</EmptyTitle>
      </EmptyHeader>
      <EmptyContent>
        <Button :loading="createPending" @click="handleCreate">
          <LucidePlus />
          {{ t('gradebook.selector.notCreatedInfo.button') }}
        </Button>
        <ErrorBanner v-if="createError" :error="createError" />
      </EmptyContent>
    </Empty>
  </template>
</template>
