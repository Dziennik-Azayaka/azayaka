<script setup lang="ts">
import GradebookGroupCard from '../components/GradebookGroupCard.vue';
import AddSubjectToGroupDialog from '../components/AddSubjectToGroupDialog.vue';
import EditSubjectInGroupDialog from '../components/EditSubjectInGroupDialog.vue';
import { useCreateGradebookGroup } from '@/api/hooks/gradebook/createGradebookGroup';
import { useGetGradebookGroups } from '@/api/hooks/gradebook/getGradebookGroups';
import { useGetGradebookStudents } from '@/api/hooks/gradebook/useGetGradebookStudents';
import { useUpdateGradebookGroup } from '@/api/hooks/gradebook/useUpdateGradebookGroup';
import type { GradebookGroupSubject } from '@/api/types/gradebook-group';
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
import { Label } from '@/components/ui/label';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { useGradebookStore } from '@/stores/gradebook';
import { LucideGraduationCap, LucidePlus } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
const gradebookStore = useGradebookStore();

const gradebookId = computed(() => gradebookStore.selectedGradebook?.id);

const {
  data: groups,
  isFetching,
  isError,
  refetch,
} = useGetGradebookGroups(gradebookId);

const {
  mutate: createGroup,
  isPending: createPending,
  error: createError,
} = useCreateGradebookGroup(gradebookId);

const {
  data: students,
  isFetching: studentsLoading,
  isError: studentsError,
  refetch: refetchStudents,
} = useGetGradebookStudents(gradebookId);

const {
  mutate: updateGroup,
  isPending: updatePending,
  error: updateError,
} = useUpdateGradebookGroup(gradebookId);

const studentAssignments = ref<Map<number, Set<number>>>(new Map());
const saveSuccess = ref(false);

function initAssignments() {
  const map = new Map<number, Set<number>>();
  for (const g of groups.value ?? []) {
    map.set(g.id, new Set(g.studentIds));
  }
  studentAssignments.value = map;
}

watch(
  () => groups.value,
  () => {
    initAssignments();
    saveSuccess.value = false;
  },
);

function isAssigned(groupId: number, studentId: number) {
  return studentAssignments.value.get(groupId)?.has(studentId) ?? false;
}

function toggleAssignment(groupId: number, studentId: number) {
  saveSuccess.value = false;
  const set = studentAssignments.value.get(groupId);
  if (!set) return;
  if (set.has(studentId)) {
    set.delete(studentId);
  } else {
    set.add(studentId);
  }
}

function handleSave() {
  saveSuccess.value = false;
  (groups.value ?? []).forEach((g) => {
    const current = studentAssignments.value.get(g.id) ?? new Set();
    const original = new Set(g.studentIds);
    if (
      original.size === current.size &&
      [...original].every((id) => current.has(id))
    ) {
      return;
    }
    updateGroup(
      { groupId: g.id, name: g.name, shortcut: g.shortcut, studentIds: [...current] },
      {
        onSuccess: () => {
          saveSuccess.value = true;
        },
      },
    );
  });
}

const studentDisplayName = (firstName: string, secondName: string | null, lastName: string) =>
  [firstName, secondName, lastName].filter(Boolean).join(' ');

const activeTab = ref('groups');
const dialogOpen = ref(false);
const groupName = ref('');
const groupShortcut = ref('');
const selectedGroupId = ref<number | null>(null);
const selectedSubject = ref<GradebookGroupSubject | null>(null);
const selectedSubjectGroupId = ref<number | null>(null);

function openEditSubject(groupId: number, subject: GradebookGroupSubject) {
  selectedSubjectGroupId.value = groupId;
  selectedSubject.value = subject;
}

function closeEditSubject() {
  selectedSubject.value = null;
  selectedSubjectGroupId.value = null;
}

function openCreateDialog() {
  groupName.value = '';
  groupShortcut.value = '';
  dialogOpen.value = true;
}

function handleCreate() {
  createGroup(
    { name: groupName.value, shortcut: groupShortcut.value },
    {
      onSuccess() {
        dialogOpen.value = false;
      },
    },
  );
}
</script>

<template>
  <Tabs v-model="activeTab" class="flex flex-col gap-4">
    <div class="flex items-center justify-between">
      <TabsList>
        <TabsTrigger value="groups">{{ t('gradebook.tabs.groups') }}</TabsTrigger>
        <TabsTrigger value="students">{{ t('gradebook.tabs.groupStudents') }}</TabsTrigger>
      </TabsList>

      <Button v-if="activeTab === 'groups'" @click="openCreateDialog">
        <LucidePlus />
        {{ t('gradebook.groups.create') }}
      </Button>
    </div>

    <TabsContent value="groups">
      <EmptyLoading v-if="isFetching" />

      <EmptyLoadingError v-else-if="isError" @refresh="refetch" />

      <template v-else-if="groups?.length">
        <GradebookGroupCard
          v-for="g in groups"
          :key="g.id"
          :group="g"
          @add-subject="selectedGroupId = g.id"
          @edit-subject="(s: GradebookGroupSubject) => openEditSubject(g.id, s)"
        />
      </template>

      <Empty v-else>
        <EmptyHeader>
          <EmptyMedia variant="icon">
            <LucideGraduationCap />
          </EmptyMedia>
          <EmptyTitle>{{ t('gradebook.groups.emptyTitle') }}</EmptyTitle>
        </EmptyHeader>
        <EmptyContent>
          <p class="text-muted-foreground">{{ t('gradebook.groups.emptyDescription') }}</p>
        </EmptyContent>
      </Empty>
    </TabsContent>

    <TabsContent value="students">
      <EmptyLoading v-if="studentsLoading || isFetching" />

      <EmptyLoadingError
        v-else-if="studentsError || isError"
        @refresh="studentsError ? refetchStudents() : refetch()"
      />

      <template v-else>
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold">{{ t('gradebook.groups.groupStudentsTitle') }}</h3>
          <Button
            :loading="updatePending"
            @click="handleSave"
          >
            {{ t('gradebook.groups.groupStudentsSave') }}
          </Button>
        </div>

        <ErrorBanner v-if="updateError" :error="updateError" class="mb-4" />

        <p v-if="saveSuccess" class="mb-4 text-sm text-green-600 dark:text-green-400">
          {{ t('gradebook.groups.groupStudentsSaved') }}
        </p>

        <Empty v-if="!groups?.length">
          <EmptyHeader>
            <EmptyMedia variant="icon">
              <LucideGraduationCap />
            </EmptyMedia>
            <EmptyTitle>{{ t('gradebook.groups.groupStudentsNoGroups') }}</EmptyTitle>
          </EmptyHeader>
        </Empty>

        <Empty v-else-if="!students?.length">
          <EmptyHeader>
            <EmptyMedia variant="icon">
              <LucideGraduationCap />
            </EmptyMedia>
            <EmptyTitle>{{ t('gradebook.groups.groupStudentsNoStudents') }}</EmptyTitle>
          </EmptyHeader>
        </Empty>

        <div v-else class="overflow-x-auto border rounded-md">
          <table class="w-full border-collapse">
            <thead>
              <tr class="bg-muted">
                <th class="sticky left-0 z-10 bg-muted px-4 py-3 text-left text-sm font-medium whitespace-nowrap border-r">
                  {{ t('gradebook.groups.groupStudentsFullName') }}
                </th>
                <th
                  v-for="g in groups"
                  :key="g.id"
                  class="px-4 py-3 text-center text-sm font-medium whitespace-nowrap min-w-[100px]"
                >
                  <div>{{ g.name }}</div>
                  <div class="text-xs text-muted-foreground">{{ g.shortcut }}</div>
                </th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="s in students"
                :key="s.studentId"
                class="border-t hover:bg-muted/50 transition-colors group"
              >
                <td class="sticky left-0 z-10 bg-background group-hover:bg-muted/50 px-4 py-3 text-sm border-r whitespace-nowrap">
                  {{ s.position }}. {{ studentDisplayName(s.studentName, s.studentSecondName, s.studentLastName) }}
                </td>
                <td
                  v-for="g in groups"
                  :key="g.id"
                  class="px-4 py-3 text-center"
                >
                  <Checkbox
                    :model-value="isAssigned(g.id, s.studentId)"
                    @update:model-value="toggleAssignment(g.id, s.studentId)"
                  />
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </template>
    </TabsContent>
  </Tabs>

  <Dialog v-model:open="dialogOpen">
    <DialogContent>
      <DialogHeader>
        <DialogTitle>{{ t('gradebook.groups.dialogTitle') }}</DialogTitle>
        <DialogDescription>{{ t('gradebook.groups.dialogDescription') }}</DialogDescription>
      </DialogHeader>
      <form class="flex flex-col gap-4" @submit.prevent="handleCreate">
        <div class="flex flex-col gap-2">
          <Label for="group-name">{{ t('gradebook.groups.name') }}</Label>
          <Input
            id="group-name"
            v-model="groupName"
            :placeholder="t('gradebook.groups.namePlaceholder')"
            required
            maxlength="255"
          />
        </div>
        <div class="flex flex-col gap-2">
          <Label for="group-shortcut">{{ t('gradebook.groups.shortcut') }}</Label>
          <Input
            id="group-shortcut"
            v-model="groupShortcut"
            :placeholder="t('gradebook.groups.shortcutPlaceholder')"
            required
            maxlength="8"
          />
        </div>
        <ErrorBanner v-if="createError" :error="createError" />
        <DialogFooter>
          <DialogClose as-child>
            <Button variant="outline" type="button">{{ t('common.actions.cancel') }}</Button>
          </DialogClose>
          <Button :loading="createPending" :disabled="!groupName || !groupShortcut">
            {{ t('common.actions.save') }}
          </Button>
        </DialogFooter>
      </form>
    </DialogContent>
  </Dialog>

  <AddSubjectToGroupDialog
    v-if="selectedGroupId !== null"
    :group-id="selectedGroupId"
    :open="selectedGroupId !== null"
    @update:open="(open: boolean) => { if (!open) selectedGroupId = null; }"
  />

  <EditSubjectInGroupDialog
    v-if="selectedSubject !== null && selectedSubjectGroupId !== null"
    :group-id="selectedSubjectGroupId"
    :subject="selectedSubject"
    :open="selectedSubject !== null"
    @update:open="(open: boolean) => { if (!open) closeEditSubject(); }"
  />
</template>
