<script setup lang="ts">
import type { SubjectValues } from '../../forms/subject';
import SubjectForm from './SubjectForm.vue';
import { useEditSubject } from '@/api/hooks/subject/editSubject';
import { useToggleSubjectActivity } from '@/api/hooks/subject/toggleSubjectActivity';
import { Button } from '@/components/ui/button';
import {
  Dialog,
  DialogClose,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from '@/components/ui/dialog';
import { LucideArchive } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps<{ subjectId: number; initialValues: SubjectValues; active: boolean }>();

const { t } = useI18n();
const dialogOpen = ref(false);

const { mutate: edit, isPending: isEditPending, error: editError } = useEditSubject();
const {
  mutate: toggleActivity,
  isPending: isToggleActivityPending,
  error: toggleActivityError,
} = useToggleSubjectActivity();

const isPending = computed(() => isEditPending.value || isToggleActivityPending.value);
const error = computed(() => editError.value || toggleActivityError.value);

const onSubmit = (values: SubjectValues) =>
  edit(
    { id: props.subjectId, body: values },
    {
      onSuccess: () => {
        dialogOpen.value = false;
      },
    },
  );
</script>

<template>
  <Dialog v-model:open="dialogOpen">
    <DialogTrigger as-child>
      <slot />
    </DialogTrigger>
    <DialogContent>
      <DialogHeader>
        <DialogTitle>{{ t('administrator.subjects.edit') }}</DialogTitle>
        <DialogDescription>{{ t('common.requiredFieldsInfo') }}</DialogDescription>
      </DialogHeader>
      <SubjectForm
        @submit="onSubmit"
        :is-pending="isPending"
        :error="error"
        :initial-values="initialValues"
      >
        <DialogFooter>
          <DialogClose as-child>
            <Button variant="outline" type="button">{{ t('common.actions.cancel') }}</Button>
          </DialogClose>
          <div class="flex-1" />
          <Button
            :disabled="isPending"
            :loading="isToggleActivityPending"
            type="button"
            variant="destructive"
            @click="() => toggleActivity(subjectId)"
          >
            <LucideArchive />
            {{ active ? t('common.actions.archive') : t('common.actions.unarchive') }}
          </Button>
          <Button :disabled="isPending" :loading="isEditPending">
            {{ t('common.actions.save') }}
          </Button>
        </DialogFooter>
      </SubjectForm>
    </DialogContent>
  </Dialog>
</template>
