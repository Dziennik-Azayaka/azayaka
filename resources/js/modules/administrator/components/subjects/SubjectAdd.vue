<script setup lang="ts">
import type { SubjectValues } from '../../forms/subject';
import SubjectForm from './SubjectForm.vue';
import { useAddSubject } from '@/api/hooks/subject/addSubject';
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
import { LucidePlus } from 'lucide-vue-next';
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
const dialogOpen = ref(false);

const { mutate: add, isPending, error } = useAddSubject();

const onSubmit = (values: SubjectValues) =>
  add(values, {
    onSuccess: () => {
      dialogOpen.value = false;
    },
  });
</script>

<template>
  <Dialog v-model:open="dialogOpen">
    <DialogTrigger as-child>
      <Button>
        <LucidePlus />
        {{ t('administrator.subjects.add') }}
      </Button>
    </DialogTrigger>
    <DialogContent>
      <DialogHeader>
        <DialogTitle>{{ t('administrator.subjects.add') }}</DialogTitle>
        <DialogDescription>{{ t('common.requiredFieldsInfo') }}</DialogDescription>
      </DialogHeader>
      <SubjectForm @submit="onSubmit" :is-pending="isPending" :error="error">
        <DialogFooter>
          <DialogClose as-child>
            <Button variant="outline" type="button">{{ t('common.actions.cancel') }}</Button>
          </DialogClose>
          <Button :loading="isPending">{{ t('common.actions.save') }}</Button>
        </DialogFooter>
      </SubjectForm>
    </DialogContent>
  </Dialog>
</template>
