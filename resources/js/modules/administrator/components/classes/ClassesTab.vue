<script setup lang="ts">
import ClassTable from './ClassTable.vue';
import type { GetClassFilter } from '@/api/dtos/class';
import { useGetClasses } from '@/api/hooks/classes/getClasses';
import type { SchoolUnit } from '@/api/types/school-structure';
import { EmptyLoading, EmptyLoadingError } from '@/components/ui/empty';
import type { UseQueryReturnType } from '@tanstack/vue-query';
import { computed } from 'vue';

const props = defineProps<{
  tab: GetClassFilter;
  schoolUnitsQuery: UseQueryReturnType<SchoolUnit[], Error>;
}>();

const {
  data: classes,
  isFetching: isClassesFetching,
  isError: isClassesError,
} = useGetClasses(computed(() => props.tab));

const isLoading = computed(() => props.schoolUnitsQuery.isPending.value || isClassesFetching.value);
const isError = computed(() => props.schoolUnitsQuery.isError.value || isClassesError.value);
</script>

<template>
  <EmptyLoading v-if="isLoading" />
  <EmptyLoadingError v-else-if="isError" />
  <ClassTable
    v-else-if="props.schoolUnitsQuery.data.value && classes"
    :classes="classes"
    :school-units="props.schoolUnitsQuery.data.value"
    :show-current-level="tab === 'current'"
  />
</template>
