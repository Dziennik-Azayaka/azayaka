<script setup lang="ts">
import { useGetClassUnits } from '@/api/hooks/class-unit/getClassUnits';
import { useGetGradebooks } from '@/api/hooks/gradebook/getGradebooks';
import { Label } from '@/components/ui/label';
import {
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select';
import { useGradebookStore } from '@/stores/gradebook';
import type { AcceptableValue } from 'reka-ui';
import { computed, ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import { useRoute, useRouter } from 'vue-router';

const { t } = useI18n();
const router = useRouter();
const route = useRoute();
const gradebookStore = useGradebookStore();

const { data: classUnits } = useGetClassUnits();

const selectedClassUnitId = ref<number | undefined>();
const selectedSchoolYear = ref<number | undefined>();

function getCurrentSchoolYear(): number {
  const now = new Date();
  const year = now.getFullYear();
  const month = now.getMonth() + 1;
  return month >= 9 ? year : year - 1;
}

const currentSchoolYear = getCurrentSchoolYear();

const allSchoolYears = computed(() => {
  const years = new Set<number>();
  for (const cu of classUnits.value ?? []) {
    const start = cu.startingClassificationPeriodYear;
    const length = cu.teachingCycleLength;
    for (let i = 0; i < length; i++) {
      years.add(start + i);
    }
  }
  return Array.from(years).sort((a, b) => a - b);
});

watch(
  allSchoolYears,
  (years) => {
    if (years.length > 0 && years.includes(currentSchoolYear)) {
      selectedSchoolYear.value = currentSchoolYear;
    }
  },
  { immediate: true },
);

const classUnitsForSelectedYear = computed(() => {
  if (!selectedSchoolYear.value) return classUnits.value ?? [];
  return (classUnits.value ?? []).filter((cu) => {
    const start = cu.startingClassificationPeriodYear;
    const end = start + cu.teachingCycleLength - 1;
    return selectedSchoolYear.value! >= start && selectedSchoolYear.value! <= end;
  });
});

const selectedClassUnit = computed(() =>
  classUnits.value?.find((cu) => cu.id === selectedClassUnitId.value),
);

const schoolUnitId = computed(() => selectedClassUnit.value?.schoolUnit.id);

const { data: gradebooks, isFetching: gradebooksLoading } = useGetGradebooks(
  schoolUnitId,
  selectedClassUnitId,
  selectedSchoolYear,
);

const existingGradebook = computed(() => gradebooks.value?.[0] ?? null);

const canCheck = computed(() => selectedClassUnitId.value && selectedSchoolYear.value);

function navigateToView() {
  gradebookStore.setSelection(selectedClassUnit.value!, selectedSchoolYear.value!);
  gradebookStore.setGradebook(existingGradebook.value ?? null);

  const currentChildRoute = String(route.name ?? '');
  const isGradebookChild = currentChildRoute.startsWith('gradebook.view.');
  const targetName = isGradebookChild ? route.name! : 'gradebook.view.students';

  router.push({
    name: targetName,
    params: {
      ...route.params,
      classUnitId: selectedClassUnitId.value!,
      schoolYear: selectedSchoolYear.value!,
    },
  });
}

watch([canCheck, gradebooksLoading], ([can, gbLoading]) => {
  if (!can || gbLoading) return;
  navigateToView();
});

function onSchoolYearChange(value: AcceptableValue) {
  selectedSchoolYear.value = Number(value);
  selectedClassUnitId.value = undefined;
}

function onClassUnitChange(value: AcceptableValue) {
  selectedClassUnitId.value = Number(value);
}
</script>

<template>
  <div class="space-y-1.5 mx-2 mb-2">
    <Label>{{ t('gradebook.selector.schoolYear') }}</Label>
    <Select :model-value="selectedSchoolYear?.toString()" @update:model-value="onSchoolYearChange">
      <SelectTrigger class="w-full bg-background">
        <SelectValue :placeholder="t('gradebook.selector.schoolYearPlaceholder')" />
      </SelectTrigger>
      <SelectContent>
        <SelectGroup>
          <SelectItem v-for="year in allSchoolYears" :key="year" :value="year.toString()">
            {{ year }}/{{ year + 1 }}
          </SelectItem>
        </SelectGroup>
      </SelectContent>
    </Select>
  </div>

  <div class="space-y-1.5 mx-2 mb-2">
    <Label>{{ t('gradebook.selector.classUnit') }}</Label>
    <Select :model-value="selectedClassUnitId?.toString()" @update:model-value="onClassUnitChange">
      <SelectTrigger class="w-full bg-background">
        <SelectValue :placeholder="t('gradebook.selector.classUnitPlaceholder')" />
      </SelectTrigger>
      <SelectContent>
        <SelectGroup>
          <SelectItem
            v-for="unit in classUnitsForSelectedYear"
            :key="unit.id"
            :value="unit.id.toString()"
          >
            {{ unit.mark }}{{ unit.alias ? ' - ' + unit.alias : '' }} ({{
              unit.schoolUnit.shortName
            }})
          </SelectItem>
        </SelectGroup>
      </SelectContent>
    </Select>
  </div>
</template>
