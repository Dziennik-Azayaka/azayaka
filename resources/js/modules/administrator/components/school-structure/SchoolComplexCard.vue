<script setup lang="ts">
import SchoolComplexEdit from './SchoolComplexEdit.vue';
import SchoolUnitCard from './SchoolUnitCard.vue';
import SchoolUnitCreate from './SchoolUnitCreate.vue';
import type { SchoolComplex } from '@/api/types/school-structure';

defineProps<{ complex: SchoolComplex }>();
</script>

<template>
  <li class="border rounded-md overflow-hidden not-sm:px-3 p-5 space-y-4">
    <div class="flex justify-between not-sm:flex-col sm:items-center gap-3">
      <h2 class="font-semibold">{{ complex.name }}</h2>
      <div class="flex gap-3">
        <SchoolUnitCreate :complex-id="complex.id" />
        <SchoolComplexEdit :complex-id="complex.id" :initial-values="complex" />
      </div>
    </div>

    <ul class="space-y-6">
      <SchoolUnitCard v-for="unit in complex.units" :key="unit.id" :unit="unit" />
    </ul>
  </li>
</template>
