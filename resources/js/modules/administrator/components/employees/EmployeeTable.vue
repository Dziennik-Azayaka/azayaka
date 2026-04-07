<script lang="ts" setup>
import EmployeeEdit from './EmployeeEdit.vue';
import EmployeeTableRoles from './EmployeeTableRoles.vue';
import type { Employee, EmployeeRole } from '@/api/types/employee';
import { Input } from '@/components/ui/input';
import TableTemplate from '@/components/ui/table/TableTemplate.vue';
import {
  createColumnHelper,
  getCoreRowModel,
  getFilteredRowModel,
  getSortedRowModel,
  useVueTable,
} from '@tanstack/vue-table';
import { h, ref } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps<{ employees: Employee[] }>();
const { t } = useI18n();

const columnHelper = createColumnHelper<Employee>();
const columns = [
  columnHelper.accessor('lastName', {
    header: () => t('common.data.lastName'),
  }),
  columnHelper.accessor('firstName', {
    header: () => t('common.data.firstName'),
  }),
  columnHelper.accessor('shortcut', {
    header: () => t('common.data.short'),
  }),
  columnHelper.accessor('roles', {
    header: () => t('common.data.roles'),
    cell: ({ row }) => h(EmployeeTableRoles, { roles: row.getValue('roles') as Set<EmployeeRole> }),
  }),
];

const searchQuery = ref('');

const table = useVueTable({
  get data() {
    return props.employees;
  },
  getRowId: (employee) => employee.id.toString(),
  columns,
  getCoreRowModel: getCoreRowModel(),
  getFilteredRowModel: getFilteredRowModel(),
  getSortedRowModel: getSortedRowModel(),
  onGlobalFilterChange: (val) => {
    searchQuery.value = val;
  },
  globalFilterFn: (row, _, filterValue) => {
    const { firstName, lastName, shortcut } = row.original;

    const combined = `${lastName} ${firstName} ${shortcut}`.toLowerCase();
    return combined.includes(filterValue.toLowerCase());
  },
  state: {
    get globalFilter() {
      return searchQuery.value;
    },
    sorting: [
      {
        id: 'lastName',
        desc: false,
      },
      {
        id: 'firstName',
        desc: false,
      },
      {
        id: 'shortcut',
        desc: false,
      },
    ],
  },
});
</script>

<template>
  <div class="flex items-stretch md:items-center not-md:flex-col gap-1.5 md:gap-4 py-4">
    <Input
      :aria-label="t('administrator.employees.tableSearch')"
      :placeholder="t('administrator.employees.tableSearch')"
      class="md:max-w-sm"
      v-model="searchQuery"
    />
  </div>
  <TableTemplate :table="table">
    <template #row="{ templateRow, row }">
      <EmployeeEdit
        :employee-id="row.original.id"
        :initial-values="{ ...row.original, roles: [...row.original.roles] }"
        :is-active="row.original.active"
      >
        <component :is="templateRow" />
      </EmployeeEdit>
    </template>
  </TableTemplate>
</template>
