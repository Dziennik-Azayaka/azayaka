<script setup lang="ts">
import ClassificationPeriodsChange from './ClassificationPeriodsChange.vue';
import type { ClassificationPeriod } from '@/api/types/classification-period';
import type { SchoolUnit } from '@/api/types/school-structure';

defineProps<{
  unit: SchoolUnit;
  schoolYear: number;
  periods: ClassificationPeriod[];
  showUnitName: boolean;
}>();
</script>

<template>
  <div
    class="flex justify-between items-center gap-3 not-md:flex-col not-md:items-stretch"
    :class="{ 'justify-end!': !showUnitName }"
  >
    <p class="font-semibold text-base" v-if="showUnitName">{{ unit.name }}</p>
    <ClassificationPeriodsChange
      :unit="unit"
      :school-year="schoolYear"
      :periods="periods"
      :show-unit-name="showUnitName"
    />
  </div>
</template>
