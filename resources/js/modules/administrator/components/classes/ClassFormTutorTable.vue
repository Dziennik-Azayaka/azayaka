<script lang="ts" setup>
import type { FormTutor } from '@/api/types/class';
import TableTemplate from '@/components/ui/table/TableTemplate.vue';
import {
  createColumnHelper,
  getCoreRowModel,
  useVueTable,
  type ColumnDef,
} from '@tanstack/vue-table';
import { useI18n } from 'vue-i18n';

const props = defineProps<{
  formTutors: FormTutor[];
}>();

const { t, d } = useI18n();

const columnHelper = createColumnHelper<FormTutor>();
const columns = [
  columnHelper.group({
    id: 'header',
    header: () => t('administrator.classes.data.formTutors'),
    columns: [
      columnHelper.display({
        id: 'fullName',
        header: () => t('common.data.fullName'),
        cell: ({ row }) => `${row.original.lastName} ${row.original.firstName}`,
      }),
      columnHelper.accessor('dateFrom', {
        header: () => t('common.data.dateFrom'),
        cell: ({ getValue }) => d(getValue(), 'short'),
      }),
      columnHelper.accessor('dateTo', {
        header: () => t('common.data.dateTo'),
        cell: ({ getValue }) => d(getValue(), 'short'),
      }),
    ],
  }),
];

const table = useVueTable({
  get data() {
    return props.formTutors;
  },
  getRowId: (tutor) => `${tutor.employeeId}`,
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
