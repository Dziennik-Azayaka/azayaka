<script lang="ts" setup>
import ClassificationPeriodTableHeader from './ClassificationPeriodTableHeader.vue';
import type { ClassificationPeriod } from '@/api/types/classification-period';
import type { SchoolUnit } from '@/api/types/school-structure';
import TableTemplate from '@/components/ui/table/TableTemplate.vue';
import { createColumnHelper, getCoreRowModel, useVueTable } from '@tanstack/vue-table';
import { h } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps<{
  periods: ClassificationPeriod[];
  unit: SchoolUnit;
  schoolYear: number;
  showUnitName: boolean;
}>();
const { t, d } = useI18n();

const columnHelper = createColumnHelper<ClassificationPeriod>();
const columns = [
  columnHelper.group({
    id: 'header',
    header: () =>
      h(ClassificationPeriodTableHeader, {
        unit: props.unit,
        periods: props.periods,
        schoolYear: props.schoolYear,
        showUnitName: props.showUnitName,
      }),
    columns: [
      columnHelper.accessor('number', {
        header: () => t('administrator.classificationPeriods.periodNumber'),
        cell: ({ getValue }) => `${getValue()}.`,
      }),
      columnHelper.accessor('start', {
        header: () => t('administrator.classificationPeriods.periodStart'),
        cell: ({ getValue }) => d(getValue(), 'short'),
      }),
      columnHelper.accessor('end', {
        header: () => t('administrator.classificationPeriods.periodEnd'),
        cell: ({ getValue }) => d(getValue(), 'short'),
      }),
    ],
  }),
];

const table = useVueTable({
  get data() {
    return props.periods;
  },
  getRowId: (period) => period.id.toString(),
  columns,
  getCoreRowModel: getCoreRowModel(),
});
</script>

<template>
  <TableTemplate :table="table">
    <template #row="{ templateRow }">
      <component :is="templateRow" class="cursor-default!" />
    </template>
  </TableTemplate>
</template>
