<script lang="ts" setup>
import type { Class } from '@/api/types/class';
import type { SchoolUnit } from '@/api/types/school-structure';
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select';
import TableTemplate from '@/components/ui/table/TableTemplate.vue';
import { schoolYearString } from '@/lib/utils';
import {
  createColumnHelper,
  getCoreRowModel,
  getFacetedUniqueValues,
  getFilteredRowModel,
  getSortedRowModel,
  useVueTable,
  type ColumnDef,
} from '@tanstack/vue-table';
import { useI18n } from 'vue-i18n';

const props = defineProps<{
  classes: Class[];
  schoolUnits: SchoolUnit[];
  showCurrentLevel: boolean;
}>();
const { t } = useI18n();

const columnHelper = createColumnHelper<Class>();
// eslint-disable-next-line @typescript-eslint/no-explicit-any
const columns: ColumnDef<Class, any>[] = [
  columnHelper.accessor('startingClassificationPeriodYear', {
    header: () => t('administrator.classes.data.startingSchoolYear'),
    cell: ({ row }) => {
      let text = schoolYearString(row.original.startingClassificationPeriodYear);
      const periodNumber = row.original.startingClassificationPeriodNumber;
      if (periodNumber !== 1) text += `(${periodNumber} ${t('common.data.period').toLowerCase()})`;

      return text;
    },
    filterFn: (row, columnId, filterValue) => filterValue === "all" || row.getValue(columnId) === filterValue,
  }),
  columnHelper.accessor('schoolUnitId', {
    header: () => t('common.data.schoolUnit'),
    cell: ({ row }) =>
      props.schoolUnits.find((unit) => unit.id === row.original.schoolUnitId)?.shortName ?? '',
    filterFn: (row, columnId, filterValue) => filterValue === "all" || row.getValue(columnId) === filterValue,
  }),
  columnHelper.accessor('mark', {
    header: () => t('administrator.classes.data.mark'),
  }),
  columnHelper.accessor('alias', {
    header: () => t('administrator.classes.data.alias'),
  }),
];

if (props.showCurrentLevel) {
  columns.push(
    columnHelper.accessor('level', { header: () => t('administrator.classes.data.level') }),
  );
}

const table = useVueTable({
  get data() {
    return props.classes;
  },
  getRowId: (class_) => class_.id.toString(),
  columns,
  getCoreRowModel: getCoreRowModel(),
  getFilteredRowModel: getFilteredRowModel(),
  getSortedRowModel: getSortedRowModel(),
  getFacetedUniqueValues: getFacetedUniqueValues(),
  state: {
    sorting: [
      {
        id: 'startingClassificationPeriodYear',
        desc: false,
      },
      {
        id: 'schoolUnitId',
        desc: false,
      },
      {
        id: 'mark',
        desc: false,
      },
    ],
  },
});
</script>

<template>
  <div class="w-full">
    <div class="flex items-stretch md:items-center not-md:flex-col gap-1.5 md:gap-4 py-4">
      <Select
        :model-value="
          table.getColumn('startingClassificationPeriodYear')?.getFilterValue() ?? 'all'
        "
        :aria-label="t('administrator.classes.tableFilters.schoolYear.label')"
        @update:model-value="
          table.getColumn('startingClassificationPeriodYear')?.setFilterValue($event)
        "
      >
        <SelectTrigger class="not-lg:w-full">
          <span class="space-x-0.5">
            <span class="text-muted-foreground">{{ t('administrator.classes.tableFilters.schoolYear.label') }}: </span>
            <SelectValue />
          </span>
        </SelectTrigger>
        <SelectContent>
          <SelectItem value="all">{{ t('administrator.classes.tableFilters.schoolYear.all') }}</SelectItem>
          <SelectItem
            v-for="id in [
              ...table
                .getColumn('startingClassificationPeriodYear')!
                .getFacetedUniqueValues()
                .keys(),
            ]
              .sort()
              .reverse()"
            :key="id"
            :value="id"
          >
            {{ schoolYearString(id) }}
          </SelectItem>
        </SelectContent>
      </Select>
      <Select
        :model-value="
          table.getColumn('schoolUnitId')?.getFilterValue() ?? 'all'
        "
        :aria-label="t('administrator.classes.tableFilters.schoolUnit.label')"
        @update:model-value="
          table.getColumn('schoolUnitId')?.setFilterValue($event)
        "
      >
        <SelectTrigger class="not-lg:w-full">
          <span class="space-x-0.5">
            <span class="text-muted-foreground">{{ t('administrator.classes.tableFilters.schoolUnit.label') }}: </span>
            <SelectValue />
          </span>
        </SelectTrigger>
        <SelectContent>
          <SelectItem value="all">{{ t('administrator.classes.tableFilters.schoolUnit.all') }}</SelectItem>
          <SelectItem
            v-for="unit in schoolUnits"
            :key="unit.id"
            :value="unit.id"
          >
            {{ unit.shortName }}
          </SelectItem>
        </SelectContent>
      </Select>
    </div>

    <TableTemplate :table="table">
      <template #row="{ templateRow }">
        <component :is="templateRow" />
      </template>
    </TableTemplate>
  </div>
</template>
