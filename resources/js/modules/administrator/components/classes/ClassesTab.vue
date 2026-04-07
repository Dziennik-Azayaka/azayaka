<script setup lang="ts">
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
  <template v-else-if="props.schoolUnitsQuery.data && classes">
    <p class="my-5 text-xl font-semibold">Classes</p>
    <pre class="border rounded-md bg-accent text-xs font-mono p-2">{{ classes }}</pre>
    <p class="my-5 text-xl font-semibold">School units</p>
    <pre class="border rounded-md bg-accent text-xs font-mono p-2">
      {{ props.schoolUnitsQuery.data }}
    </pre>
  </template>
</template>
