<script setup lang="ts">
import { useGetClassUnits } from '@/api/hooks/class-unit/getClassUnits';
import { useGetFilteredStudents } from '@/api/hooks/student/getFilteredStudents';
import { type StudentStatusFilter } from '@/api/services/student';
import type { Student } from '@/api/types/student';
import { Button } from '@/components/ui/button';
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuCheckboxItem,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { EmptyLoading, EmptyLoadingError } from '@/components/ui/empty';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import TableTemplate from '@/components/ui/table/TableTemplate.vue';
import { valueUpdater } from '@/components/ui/table/utils';
import {
  createColumnHelper,
  FlexRender,
  getCoreRowModel,
  useVueTable,
  type SortingState,
  type VisibilityState,
} from '@tanstack/vue-table';
import {
  LucideChevronLeft,
  LucideChevronRight,
  LucideChevronDown,
  LucideColumns,
  LucideFilter,
} from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps<{ registryId: number; schoolUnitId?: number }>();

const { d, t } = useI18n();

const page = ref(1);
const gender = ref<string | null>(null);
const birthYear = ref<string>();
const status = ref<StudentStatusFilter | null>(null);
const classUnitId = ref<string | null>(null);

const sorting = ref<SortingState>([]);
const sortField = computed(() => sorting.value[0]?.id ?? null);
const sortOrder = computed(() => (sorting.value[0]?.desc ? 'desc' : 'asc'));

const classUnitIdNumber = computed(() => {
  const v = classUnitId.value;
  return v !== null && v !== '' ? Number(v) : null;
});

const { data: allClassUnits } = useGetClassUnits();
const classUnits = computed(() =>
  (allClassUnits.value ?? []).filter(
    (cu) => props.schoolUnitId === undefined || cu.schoolUnit.id === props.schoolUnitId,
  ),
);

const {
  data: resource,
  isFetching,
  isError,
  refetch,
} = useGetFilteredStudents(
  props.registryId,
  page,
  classUnitIdNumber,
  sortField,
  sortOrder,
  birthYear,
  gender,
  status,
);

watch([gender, birthYear, status, classUnitId], () => {
  page.value = 1;
});

watch(sorting, () => {
  page.value = 1;
});

const columnVisibility = ref<VisibilityState>({});
const columnHelper = createColumnHelper<Student>();

const columns = [
  columnHelper.accessor('id', {
    header: () => t('common.data.numberShort'),
  }),
  columnHelper.accessor(
    (student) => student.person.pesel ?? student.person.alternateIdentityDocument,
    {
      id: 'peselAlternateDocument',
      header: () => `${t('common.data.pesel')} / ${t('common.data.alternateDocument')}`,
      enableSorting: false,
    },
  ),
  columnHelper.accessor('person.lastName', {
    header: () => t('common.data.lastName'),
  }),
  columnHelper.accessor(
    (student) =>
      student.person.secondName
        ? `${student.person.firstName} ${student.person.secondName}`
        : student.person.firstName,
    { id: 'names', header: () => t('common.data.names'), enableSorting: false },
  ),
  columnHelper.accessor('person.birthdate', {
    id: 'birthdate',
    header: () => t('common.data.birthDate'),
    cell: ({ getValue }) => d(getValue(), 'numericDate'),
  }),
  columnHelper.accessor('person.residenceAddress.town', {
    header: () => 'Miejsce zamieszkania',
    enableSorting: false,
  }),
  columnHelper.accessor('admissionDate', {
    header: () => t('common.data.admissionDate'),
    cell: ({ getValue }) => d(getValue(), 'numericDate'),
  }),
];

const table = useVueTable({
  get data() {
    return resource.value?.data ?? [];
  },
  getRowId: (student) => student.id.toString(),
  columns,
  getCoreRowModel: getCoreRowModel(),
  manualPagination: true,
  manualFiltering: true,
  manualSorting: true,
  onColumnVisibilityChange: (updaterOrValue) => valueUpdater(updaterOrValue, columnVisibility),
  onSortingChange: (updaterOrValue) => valueUpdater(updaterOrValue, sorting),
  state: {
    get columnVisibility() {
      return columnVisibility.value;
    },
    get sorting() {
      return sorting.value;
    },
  },
});
</script>

<template>
  <div class="mb-3 flex gap-3">
    <DropdownMenu>
      <DropdownMenuTrigger as-child>
        <Button variant="outline">
          <LucideColumns />
          {{ t('common.table.columns') }}
          <LucideChevronDown />
        </Button>
      </DropdownMenuTrigger>
      <DropdownMenuContent align="end">
        <DropdownMenuCheckboxItem
          v-for="column in table.getAllColumns().filter((column) => column.getCanHide())"
          :key="column.id"
          class="capitalize"
          :model-value="column.getIsVisible()"
          @update:model-value="(value) => column.toggleVisibility(!!value)"
        >
          <FlexRender :render="column.columnDef.header" />
        </DropdownMenuCheckboxItem>
      </DropdownMenuContent>
    </DropdownMenu>
    <Popover>
      <PopoverTrigger>
        <Button variant="outline">
          <LucideFilter />
          {{ t('common.table.filters') }}
          <LucideChevronDown />
        </Button>
      </PopoverTrigger>
      <PopoverContent>
        <div class="grid grid-cols-[max-content_1fr] items-center gap-3">
          <div>
            <Label for="status-select">Status</Label>
          </div>
          <div>
            <Select v-model="status">
              <SelectTrigger id="status-select" class="w-full">
                <SelectValue :placeholder="t('common.actions.select')" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="">--</SelectItem>
                <SelectItem value="active">Aktywny</SelectItem>
                <SelectItem value="inactive">Nieaktywny</SelectItem>
                <SelectItem value="trashed">Omyłkowy</SelectItem>
              </SelectContent>
            </Select>
          </div>
          <div>
            <Label for="gender-select">Płeć</Label>
          </div>
          <div>
            <Select v-model="gender">
              <SelectTrigger id="gender-select" class="w-full">
                <SelectValue :placeholder="t('common.actions.select')" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="">--</SelectItem>
                <SelectItem value="female">Kobieta</SelectItem>
                <SelectItem value="male">Mężczyzna</SelectItem>
              </SelectContent>
            </Select>
          </div>
          <div>
            <Label for="birthYear-input">Rok urodzenia</Label>
          </div>
          <div>
            <Input id="birthYear-input" v-model="birthYear" />
          </div>
          <div>
            <Label for="class-unit-select">{{ t('gradebook.selector.classUnit') }}</Label>
          </div>
          <div>
            <Select v-model="classUnitId">
              <SelectTrigger id="class-unit-select" class="w-full">
                <SelectValue :placeholder="t('common.actions.select')" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="">--</SelectItem>
                <SelectItem v-for="cu in classUnits" :key="cu.id" :value="String(cu.id)">
                  {{ cu.mark }}
                </SelectItem>
              </SelectContent>
            </Select>
          </div>
        </div>
      </PopoverContent>
    </Popover>
  </div>

  <EmptyLoading v-if="isFetching" />
  <EmptyLoadingError v-else-if="isError" @refresh="refetch" />
  <template v-else-if="resource">
    <TableTemplate :table="table" />
    <div class="flex justify-between mt-3">
      <div class="text-sm text-muted-foreground">
        Strona {{ page }} z {{ resource.meta.last_page }}
      </div>
      <div class="flex gap-2">
        <Button variant="outline" @click="page--" :disabled="page === 1">
          <LucideChevronLeft />
          {{ t('common.pagination.previousPage') }}
        </Button>
        <Button variant="outline" @click="page++" :disabled="resource.meta.last_page === page">
          {{ t('common.pagination.nextPage') }}
          <LucideChevronRight />
        </Button>
      </div>
    </div>
  </template>
</template>
