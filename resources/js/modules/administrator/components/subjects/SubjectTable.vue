<script lang="ts" setup>
import SubjectEdit from './SubjectEdit.vue';
import type { Subject } from '@/api/types/subject';
import { Input } from '@/components/ui/input';
import TableTemplate from '@/components/ui/table/TableTemplate.vue';
import {
  createColumnHelper,
  getCoreRowModel,
  getFilteredRowModel,
  getSortedRowModel,
  useVueTable,
} from '@tanstack/vue-table';
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps<{ subjects: Subject[] }>();
const { t } = useI18n();

const columnHelper = createColumnHelper<Subject>();
const columns = [
  columnHelper.accessor('name', {
    header: () => t('common.data.name'),
  }),
  columnHelper.accessor('shortcut', {
    header: () => t('common.data.short'),
  }),
];

const searchQuery = ref('');

const table = useVueTable({
  get data() {
    return props.subjects;
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
    const { name, shortcut } = row.original;

    const combined = `${name} ${shortcut}`.toLowerCase();
    return combined.includes(filterValue.toLowerCase());
  },
  state: {
    get globalFilter() {
      return searchQuery.value;
    },
    sorting: [
      {
        id: 'name',
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
  <div class="w-full">
    <div class="flex items-stretch md:items-center not-md:flex-col gap-1.5 md:gap-4 py-4">
      <Input
        :aria-label="t('administrator.subjects.tableSearch')"
        :placeholder="t('administrator.subjects.tableSearch')"
        class="md:max-w-sm"
        v-model="searchQuery"
      />
    </div>

    <TableTemplate :table="table">
      <template #row="{ templateRow, row }">
        <SubjectEdit
          :subject-id="row.original.id"
          :initial-values="row.original"
          :active="row.original.active"
        >
          <component :is="templateRow" />
        </SubjectEdit>
      </template>
    </TableTemplate>
  </div>
</template>
