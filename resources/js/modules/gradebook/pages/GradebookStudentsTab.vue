<script setup lang="ts">
import { useAttachStudentsToGradebook } from '@/api/hooks/gradebook/useAttachStudentsToGradebook';
import { useGetGradebookStudents } from '@/api/hooks/gradebook/useGetGradebookStudents';
import { useGetStudentRegistryId } from '@/api/hooks/student-registry/getStudentRegistryId';
import { useGetStudents } from '@/api/hooks/student/useGetStudents';
import type { GradebookStudent } from '@/api/types/gradebook-student';
import type { Student } from '@/api/types/student';
import { ErrorBanner } from '@/components/ui/banner';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import {
  Dialog,
  DialogClose,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog';
import { Empty, EmptyContent, EmptyHeader, EmptyMedia, EmptyTitle } from '@/components/ui/empty';
import { EmptyLoading } from '@/components/ui/empty';
import EmptyLoadingError from '@/components/ui/empty/EmptyLoadingError.vue';
import { Input } from '@/components/ui/input';
import TableCheckbox from '@/components/ui/table/TableCheckbox.vue';
import TableTemplate from '@/components/ui/table/TableTemplate.vue';
import { useGradebookStore } from '@/stores/gradebook';
import {
  createColumnHelper,
  getCoreRowModel,
  getFilteredRowModel,
  getSortedRowModel,
  useVueTable,
} from '@tanstack/vue-table';
import { LucidePlus, LucideUsers } from 'lucide-vue-next';
import { computed, h, ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
const gradebookStore = useGradebookStore();

const gradebookId = computed(() => gradebookStore.selectedGradebook?.id);
const schoolUnitId = computed(() => gradebookStore.selectedClassUnit?.schoolUnit.id);

const {
  data: attachedStudents,
  isFetching: attachedLoading,
  isError: attachedError,
  refetch: refetchAttached,
} = useGetGradebookStudents(gradebookId);

const { data: registryId, isFetching: registryIdLoading } = useGetStudentRegistryId(schoolUnitId);

const registryIdOrUndefined = computed(() => registryId.value ?? undefined);

const {
  data: registryStudents,
  isFetching: registryStudentsLoading,
  refetch: refetchStudents,
} = useGetStudents(registryIdOrUndefined);

const {
  mutate: attachStudents,
  isPending: attachPending,
  error: attachError,
} = useAttachStudentsToGradebook();

const dialogOpen = ref(false);
const searchQuery = ref('');
const selectedNewIds = ref<Set<number>>(new Set());

watch(dialogOpen, (open) => {
  if (open && registryId.value) {
    refetchStudents();
  }
});

const attachedStudentIds = computed(
  () => new Set((attachedStudents.value ?? []).map((s) => s.studentId)),
);

const availableStudents = computed(() => {
  const students = registryStudents.value?.data ?? [];
  const attached = attachedStudentIds.value;
  const query = searchQuery.value.toLowerCase().trim();

  return students.filter((s) => {
    if (attached.has(s.id)) return false;
    if (!query) return true;
    const fullName = [s.person.firstName, s.person.secondName, s.person.lastName]
      .filter(Boolean)
      .join(' ')
      .toLowerCase();
    return fullName.includes(query);
  });
});

const maxPosition = computed(() =>
  attachedStudents.value?.length ? Math.max(...attachedStudents.value.map((s) => s.position)) : 0,
);

function studentDisplayName(firstName: string, secondName: string | null, lastName: string) {
  return [firstName, secondName, lastName].filter(Boolean).join(' ');
}

function openDialog() {
  searchQuery.value = '';
  selectedNewIds.value = new Set();
  dialogOpen.value = true;
}

function handleAttach() {
  if (selectedNewIds.value.size === 0) return;

  const existingIds = (attachedStudents.value ?? []).map((s) => s.studentId);
  const existingPositions = (attachedStudents.value ?? []).map((s) => s.position);
  const newIds = [...selectedNewIds.value];
  const newPositions = newIds.map((_, i) => maxPosition.value + 1 + i);

  attachStudents(
    {
      gradebookId: gradebookId.value!,
      studentIds: [...existingIds, ...newIds],
      positions: [...existingPositions, ...newPositions],
    },
    {
      onSuccess: () => {
        dialogOpen.value = false;
      },
    },
  );
}

// --- Main table (attached students) ---

const mainColumnHelper = createColumnHelper<GradebookStudent>();
const mainColumns = [
  mainColumnHelper.accessor('position', {
    header: () => t('gradebook.students.position'),
    size: 96,
  }),
  mainColumnHelper.accessor(
    (row) => studentDisplayName(row.studentName, row.studentSecondName, row.studentLastName),
    {
      id: 'fullName',
      header: () => t('common.data.fullName'),
    },
  ),
];

const mainTable = useVueTable({
  get data() {
    return attachedStudents.value ?? [];
  },
  getRowId: (s) => s.studentId.toString(),
  columns: mainColumns,
  getCoreRowModel: getCoreRowModel(),
  state: {
    sorting: [{ id: 'position', desc: false }],
  },
});

// --- Dialog table (available students) ---

const dialogColumnHelper = createColumnHelper<Student>();
const dialogColumns = [
  dialogColumnHelper.display({
    id: 'select',
    header: ({ table }) =>
      h(Checkbox, {
        modelValue:
          table.getIsAllPageRowsSelected() ||
          (table.getIsSomePageRowsSelected() && 'indeterminate'),
        'onUpdate:modelValue': (value) => table.toggleAllPageRowsSelected(!!value),
        ariaLabel: () => t('common.actions.selectAll'),
      }),
    cell: ({ row }) => h(TableCheckbox<Student>, { row }),
    enableSorting: false,
    size: 48,
  }),
  dialogColumnHelper.accessor(
    (row) =>
      studentDisplayName(row.person.firstName, row.person.secondName, row.person.lastName),
    {
      id: 'fullName',
      header: () => t('common.data.fullName'),
    },
  ),
];

const dialogTable = useVueTable({
  get data() {
    return availableStudents.value;
  },
  getRowId: (s) => s.id.toString(),
  columns: dialogColumns,
  getCoreRowModel: getCoreRowModel(),
  getFilteredRowModel: getFilteredRowModel(),
  getSortedRowModel: getSortedRowModel(),
  onRowSelectionChange: (updaterOrValue) => {
    const prevSelection = Object.fromEntries(
      [...selectedNewIds.value].map((id) => [id.toString(), true]),
    );
    const newSelection =
      typeof updaterOrValue === 'function' ? updaterOrValue(prevSelection) : updaterOrValue;
    const ids = Object.entries(newSelection)
      .filter(([, selected]) => selected)
      .map(([id]) => Number(id));
    selectedNewIds.value = new Set(ids);
  },
  state: {
    get rowSelection() {
      return Object.fromEntries(
        [...selectedNewIds.value].map((id) => [id.toString(), true]),
      );
    },
  },
});
</script>

<template>
  <EmptyLoading v-if="attachedLoading" />

  <EmptyLoadingError v-else-if="attachedError" @refresh="refetchAttached" />

  <template v-else>
    <div class="flex items-center justify-between mb-4">
      <h3 class="text-lg font-semibold">{{ t('gradebook.tabs.students') }}</h3>
      <Button @click="openDialog" :disabled="!registryId">
        <LucidePlus />
        {{ t('gradebook.students.attach') }}
      </Button>
    </div>

    <div
      v-if="!registryId && !registryIdLoading"
      class="mb-4 border border-dashed border-foreground/20 rounded-md p-3 text-sm text-muted-foreground"
    >
      {{ t('gradebook.students.noRegistry') }}
    </div>

    <TableTemplate v-if="(attachedStudents ?? []).length > 0" :table="mainTable" />

    <Empty v-else>
      <EmptyHeader>
        <EmptyMedia variant="icon">
          <LucideUsers />
        </EmptyMedia>
        <EmptyTitle>{{ t('gradebook.students.emptyTitle') }}</EmptyTitle>
      </EmptyHeader>
      <EmptyContent>
        <p class="text-muted-foreground text-sm">
          {{ t('gradebook.students.emptyDescription') }}
        </p>
      </EmptyContent>
    </Empty>
  </template>

  <Dialog v-model:open="dialogOpen">
    <DialogContent class="max-w-2xl">
      <DialogHeader>
        <DialogTitle>{{ t('gradebook.students.dialogTitle') }}</DialogTitle>
        <DialogDescription>
          {{ t('gradebook.students.dialogDescription') }}
        </DialogDescription>
      </DialogHeader>

      <div
        v-if="!registryId && !registryIdLoading"
        class="py-6 text-center text-muted-foreground text-sm"
      >
        {{ t('gradebook.students.noRegistry') }}
      </div>

      <template v-else>
        <div class="space-y-4">
          <Input
            v-model="searchQuery"
            :placeholder="t('gradebook.students.searchPlaceholder')"
            type="search"
          />

          <div
            v-if="registryStudentsLoading"
            class="py-8 text-center text-muted-foreground text-sm"
          >
            {{ t('common.pleaseWait') }}
          </div>

          <template v-else>
            <div class="max-h-80 overflow-y-auto border rounded-md">
              <TableTemplate :table="dialogTable">
                <template #row="{ templateRow, row }">
                  <component :is="templateRow" class="cursor-pointer" @click="row.toggleSelected()" />
                </template>
              </TableTemplate>
            </div>

            <p class="text-sm text-muted-foreground">
              {{ t('gradebook.students.selectedCount', { count: selectedNewIds.size }) }}
            </p>
          </template>
        </div>

        <ErrorBanner v-if="attachError" :error="attachError" class="mt-2" />
      </template>

      <DialogFooter>
        <DialogClose as-child>
          <Button variant="outline">{{ t('common.actions.cancel') }}</Button>
        </DialogClose>
        <Button
          :disabled="selectedNewIds.size === 0 || !registryId"
          :loading="attachPending"
          @click="handleAttach"
        >
          {{ t('common.actions.confirm') }}
        </Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>
