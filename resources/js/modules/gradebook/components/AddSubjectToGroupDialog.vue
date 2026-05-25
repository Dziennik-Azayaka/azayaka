<script setup lang="ts">
import {useAddGradebookGroupSubject} from '@/api/hooks/gradebook/useAddGradebookGroupSubject';
import {useGetEmployees} from '@/api/hooks/employee/getEmployees';
import {useGetSubjects} from '@/api/hooks/subject/getSubjects';
import {Badge} from '@/components/ui/badge';
import {ErrorBanner} from '@/components/ui/banner';
import {Button} from '@/components/ui/button';
import {
  Dialog,
  DialogClose,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog';
import {Label} from '@/components/ui/label';
import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from '@/components/ui/popover';
import {
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectLabel,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select';
import type {Employee} from '@/api/types/employee';
import {useGradebookStore} from '@/stores/gradebook';
import DescriptionHelpDialog from '@/modules/gradebook/components/DescriptionHelpDialog.vue';
import {SUBJECT_TYPE_GROUPS} from '@/modules/gradebook/constants';
import {LucideHelpCircle, LucidePlus, LucideX} from 'lucide-vue-next';
import {computed, ref, watch} from 'vue';
import {useI18n} from 'vue-i18n';

const LABEL_TRUNCATE_LENGTH = 55;

function truncateLabel(label: string): string {
  if (label.length <= LABEL_TRUNCATE_LENGTH) return label;
  return label.slice(0, LABEL_TRUNCATE_LENGTH) + '…';
}

const props = defineProps<{ groupId: number; open: boolean }>();
const emit = defineEmits<{ 'update:open': [value: boolean] }>();

const {t} = useI18n();
const gradebookStore = useGradebookStore();

const {data: subjects} = useGetSubjects();
const {data: employees} = useGetEmployees();

const gradebookId = computed(() => gradebookStore.selectedGradebook?.id);

const {
  mutate: addSubject,
  isPending,
  error,
} = useAddGradebookGroupSubject(gradebookId);

const selectedSubjectId = ref<string>();
const selectedDescription = ref<string>();
const selectedTeachers = ref<Employee[]>([]);
const teacherPopoverOpen = ref(false);
const descriptionHelpOpen = ref(false);

const availableTeachers = computed(() => {
  if (!employees.value) return [];
  const selectedIds = new Set(selectedTeachers.value.map((t) => t.id));
  return employees.value.filter(
    (e) => e.roles.has('teacher') && !selectedIds.has(e.id),
  );
});

const canSave = computed(
  () => selectedSubjectId.value && selectedDescription.value,
);

watch(
  () => props.open,
  (open) => {
    if (open) {
      selectedSubjectId.value = undefined;
      selectedDescription.value = undefined;
      selectedTeachers.value = [];
    }
  },
);

function addTeacher(employeeId: string) {
  const employee = employees.value?.find((e) => e.id === Number(employeeId));
  if (employee) {
    selectedTeachers.value = [...selectedTeachers.value, employee];
  }
}

function removeTeacher(id: number) {
  selectedTeachers.value = selectedTeachers.value.filter((t) => t.id !== id);
}

function handleHelpSelect(value: string) {
  selectedDescription.value = value;
  descriptionHelpOpen.value = false;
}

function handleSave() {
  addSubject(
    {
      groupId: props.groupId,
      subjectId: Number(selectedSubjectId.value),
      description: selectedDescription.value!,
      teacherIds: selectedTeachers.value.map((t) => t.id),
    },
    {
      onSuccess() {
        emit('update:open', false);
      },
    },
  );
}
</script>

<template>
  <Dialog :open="open" @update:open="emit('update:open', $event)">
    <DialogContent>
      <DialogHeader>
        <DialogTitle>{{ t('gradebook.groups.addSubjectDialogTitle') }}</DialogTitle>
        <DialogDescription>
          {{ t('gradebook.groups.addSubjectDialogDescription') }}
        </DialogDescription>
      </DialogHeader>
      <form class="flex flex-col gap-4" @submit.prevent="handleSave">
        <div class="flex flex-col gap-2">
          <Label>{{ t('gradebook.groups.subject') }}</Label>
          <Select v-model="selectedSubjectId">
            <SelectTrigger class="w-full bg-background">
              <SelectValue :placeholder="t('gradebook.groups.subjectPlaceholder')"/>
            </SelectTrigger>
            <SelectContent>
              <SelectGroup>
                <SelectItem
                  v-for="s in subjects"
                  :key="s.id"
                  :value="s.id.toString()"
                >
                  {{ s.name }}
                </SelectItem>
              </SelectGroup>
            </SelectContent>
          </Select>
        </div>

        <div class="flex flex-col gap-2">
          <div class="flex items-center gap-1.5">
            <Label>{{ t('gradebook.groups.description') }}</Label>
            <Button
              variant="ghost"
              size="icon"
              class="size-5"
              type="button"
              @click="descriptionHelpOpen = true"
            >
              <LucideHelpCircle class="size-4" />
            </Button>
          </div>
          <Select v-model="selectedDescription">
            <SelectTrigger class="w-full bg-background">
              <SelectValue :placeholder="t('gradebook.groups.descriptionPlaceholder')"/>
            </SelectTrigger>
            <SelectContent>
              <SelectGroup v-for="group in SUBJECT_TYPE_GROUPS" :key="group.label">
                <SelectLabel>{{ group.label }}</SelectLabel>
                <SelectItem
                  v-for="st in group.items"
                  :key="st.value"
                  :value="st.value"
                >
                  {{ truncateLabel(st.label) }}
                </SelectItem>
              </SelectGroup>
            </SelectContent>
          </Select>
        </div>

        <div class="flex flex-col gap-2">
          <Label>{{ t('gradebook.groups.teachers') }}</Label>
          <div class="flex flex-wrap items-center gap-1.5">
            <Badge
              v-for="teacher in selectedTeachers"
              :key="teacher.id"
              variant="secondary"
              class="gap-1"
            >
              {{ teacher.firstName }} {{ teacher.lastName }}
              <button
                type="button"
                class="ml-0.5 rounded-full p-0.5 hover:bg-muted-foreground/20"
                @click="removeTeacher(teacher.id)"
              >
                <LucideX class="size-3"/>
              </button>
            </Badge>

            <Popover v-model:open="teacherPopoverOpen">
              <PopoverTrigger as-child>
                <Button variant="outline" size="icon" class="size-7" type="button">
                  <LucidePlus class="size-4"/>
                </Button>
              </PopoverTrigger>
              <PopoverContent class="w-56 p-0" align="start">
                <Select
                  :model-value="undefined"
                  @update:model-value="
                    (v) => {
                      if (v) {
                        addTeacher(String(v));
                        teacherPopoverOpen = false;
                      }
                    }
                  "
                >
                  <SelectTrigger class="w-full border-0 bg-transparent">
                    <SelectValue :placeholder="t('gradebook.groups.selectTeacher')"/>
                  </SelectTrigger>
                  <SelectContent>
                    <SelectGroup>
                      <SelectItem
                        v-for="emp in availableTeachers"
                        :key="emp.id"
                        :value="emp.id.toString()"
                      >
                        {{ emp.firstName }} {{ emp.lastName }}
                      </SelectItem>
                    </SelectGroup>
                  </SelectContent>
                </Select>
              </PopoverContent>
            </Popover>
          </div>
        </div>

        <ErrorBanner v-if="error" :error="error"/>

        <DialogFooter>
          <DialogClose as-child>
            <Button variant="outline" type="button">
              {{ t('common.actions.cancel') }}
            </Button>
          </DialogClose>
          <Button :loading="isPending" :disabled="!canSave">
            {{ t('common.actions.save') }}
          </Button>
        </DialogFooter>
      </form>
    </DialogContent>
  </Dialog>

  <DescriptionHelpDialog
    :open="descriptionHelpOpen"
    @update:open="descriptionHelpOpen = $event"
    @select="handleHelpSelect"
  />
</template>
