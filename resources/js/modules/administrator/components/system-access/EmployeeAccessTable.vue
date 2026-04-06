<script lang="ts" setup>
import AccessInfoDialog from './AccessInfoDialog.vue';
import AccessStatusBadge from './AccessStatusBadge.vue';
import MassActionPrint from './MassActionPrint.vue';
import { useDownloadEmployeeAccessPdf } from '@/api/hooks/employee/downloadEmployeeAccessPdf';
import { AccessStatus } from '@/api/types/access';
import type { EmployeeAccess } from '@/api/types/employee-access';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import {
  Select,
  SelectContent,
  SelectItem,
  SelectValue,
  SelectTrigger,
} from '@/components/ui/select';
import TableCheckbox from '@/components/ui/table/TableCheckbox.vue';
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

const props = defineProps<{ accesses: EmployeeAccess[] }>();
const { t, d } = useI18n();

const columnHelper = createColumnHelper<EmployeeAccess>();
const columns = [
  columnHelper.display({
    id: 'select',
    header: ({ table }) =>
      h(Checkbox, {
        modelValue:
          table.getIsAllPageRowsSelected() ||
          (table.getIsSomePageRowsSelected() && 'indeterminate'),
        'onUpdate:modelValue': (value) => table.toggleAllPageRowsSelected(!!value),
        ariaLabel: () => t('common.actions.selectAll'),
      }),
    cell: ({ row }) => h(TableCheckbox<EmployeeAccess>, { row }),
    enableSorting: false,
    enableHiding: false,
  }),
  columnHelper.accessor((row) => `${row.fullName} (${row.shortcut})`, {
    id: 'fullName',
    header: () => `${t('common.data.fullName')} (${t('common.data.short').toLowerCase()})`,
  }),
  columnHelper.accessor('status', {
    header: () => t('administrator.systemAccess.accessStatus'),
    cell: ({ row }) => h(AccessStatusBadge, { status: row.getValue('status') as AccessStatus }),
    filterFn: (row, columnId, filterValue) =>
      filterValue === 'all' || row.getValue(columnId) === filterValue,
  }),
  columnHelper.accessor(
    (row) => (row.status === AccessStatus.ACTIVE ? d(row.lastLoginAt, 'long') : '-'),
    {
      id: 'lastLoginAt',
      header: () => t('administrator.systemAccess.lastLoginDate'),
    },
  ),
];

const searchQuery = ref('');

const table = useVueTable({
  get data() {
    return props.accesses;
  },
  getRowId: (access) => access.id.toString(),
  columns,
  getCoreRowModel: getCoreRowModel(),
  getFilteredRowModel: getFilteredRowModel(),
  getSortedRowModel: getSortedRowModel(),
  onGlobalFilterChange: (val) => {
    searchQuery.value = val;
  },
  globalFilterFn: (row, _, filterValue) => {
    const { fullName, shortcut } = row.original;

    const combined = `${fullName} ${shortcut}`.toLowerCase();
    return combined.includes(filterValue.toLowerCase());
  },
  state: {
    get globalFilter() {
      return searchQuery.value;
    },
    sorting: [
      {
        id: 'fullName',
        desc: false,
      },
    ],
  },
});
</script>

<template>
  <div class="w-full">
    <div class="flex items-stretch md:items-center not-md:flex-col gap-1.5 md:gap-4 py-4">
      <Input
        :aria-label="t('administrator.systemAccess.tableSearch')"
        :placeholder="t('administrator.systemAccess.tableSearch')"
        class="md:max-w-sm"
        v-model="searchQuery"
      />
      <div class="flex-1"></div>
      <Select
        :model-value="table.getColumn('status')?.getFilterValue() ?? 'all'"
        :aria-label="t('administrator.systemAccess.accessStatus')"
        @update:model-value="table.getColumn('status')?.setFilterValue($event)"
      >
        <SelectTrigger class="not-lg:w-full">
          <span class="space-x-0.5">
            <span class="text-muted-foreground">
              {{ t('administrator.systemAccess.accessStatus') }}:
            </span>
            <SelectValue />
          </span>
        </SelectTrigger>
        <SelectContent>
          <SelectItem value="all">
            {{ t('administrator.systemAccess.accessStatuses.all') }}
          </SelectItem>
          <SelectItem value="inactive">
            {{ t('administrator.systemAccess.accessStatuses.inactive') }}
          </SelectItem>
          <SelectItem value="codeGenerated">
            {{ t('administrator.systemAccess.accessStatuses.codeGenerated') }}
          </SelectItem>
          <SelectItem value="active">
            {{ t('administrator.systemAccess.accessStatuses.active') }}
          </SelectItem>
        </SelectContent>
      </Select>
      <MassActionPrint
        :hook="useDownloadEmployeeAccessPdf"
        :selected="table.getSelectedRowModel().rows.map((row) => row.original)"
      />
    </div>

    <TableTemplate :table="table">
      <template #row="{ templateRow, row }">
        <AccessInfoDialog :data="row.original" access-type="employee">
          <component :is="templateRow" />
        </AccessInfoDialog>
      </template>
    </TableTemplate>
  </div>
</template>
