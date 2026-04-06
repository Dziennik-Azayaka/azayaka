<script setup lang="ts">
import { useGetClassificationPeriods } from '@/api/hooks/classification-period/getClassificationPeriods';
import { useGetSchoolUnits } from '@/api/hooks/school-structure/getSchoolUnits';
import PanelPageHeader from '@/components/panel-layout/PanelPageHeader.vue';
import { Button } from '@/components/ui/button';
import { EmptyLoading, EmptyLoadingError } from '@/components/ui/empty';
import { currentSchoolYear, schoolYearString } from '@/lib/utils';
import ClassificationPeriodTable from '@/modules/administrator/components/classification-periods/ClassificationPeriodTable.vue';
import { LucideChevronLeft, LucideChevronRight } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const schoolYear = ref(currentSchoolYear());

const {
  data: schoolUnits,
  isFetching: isUnitsFetching,
  isError: isUnitsError,
  refetch: unitsRefetch,
} = useGetSchoolUnits();

const activeUnits = computed(() => schoolUnits.value?.filter((unit) => unit.active) ?? []);
const activeUnitsIds = computed(() => activeUnits.value.map((unit) => unit.id) ?? []);
const {
  data: classificationPeriods,
  isFetching: isPeriodsFetcing,
  isError: isPeriodsError,
  refetch: periodsRefetch,
} = useGetClassificationPeriods(activeUnitsIds, schoolYear);

function refetch() {
  if (isUnitsError.value) unitsRefetch();
  else periodsRefetch();
}
</script>

<template>
  <PanelPageHeader :title="t('administrator.classificationPeriods.title')" />

  <section class="px-3 sm:px-5 py-3 gap-2 border rounded-md flex justify-between items-center">
    <p class="font-semibold">
      <span class="not-md:hidden">{{ t('common.data.schoolYear') }}</span>
      {{ schoolYearString(schoolYear) }}
    </p>
    <div class="flex gap-3">
      <Button variant="ghost" size="icon" @click="schoolYear--" :disabled="schoolYear <= 2000">
        <LucideChevronLeft :aria-label="t('common.actions.previousYear')" />
      </Button>
      <Button variant="ghost" size="icon" @click="schoolYear++" :disabled="schoolYear >= 2100">
        <LucideChevronRight :aria-label="t('common.actions.nextYear')" />
      </Button>
    </div>
  </section>
  <EmptyLoading v-if="isUnitsFetching || isPeriodsFetcing" />
  <EmptyLoadingError v-else-if="isUnitsError || isPeriodsError" @refresh="refetch" />
  <div class="space-y-3 mt-5" v-else-if="classificationPeriods">
    <ClassificationPeriodTable
      v-for="unit in activeUnits"
      :key="unit.id"
      :periods="classificationPeriods.find(({ unitId }) => unitId === unit.id)?.periods ?? []"
      :unit="unit"
      :school-year="schoolYear"
      :show-unit-name="activeUnits.length !== 1"
    />
  </div>
</template>
