<script setup lang="ts">
import type { ClassAddValues } from '../../forms/class-add';
import ClassAddGeneralForm from './ClassAddForm.vue';
import { useAddClass } from '@/api/hooks/classes/addClass';
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

const { mutate: add, isPending, error } = useAddClass();
const onSubmit = (values: ClassAddValues) =>
  add({
    ...values,
    employees: values.formTutors.map(id => ({ id })),
    alias: values.alias ?? null,
    startingClassificationPeriodId: values.startingClassificationPeriodId.id,
  });
</script>

<template>
  <Dialog v-model:open="dialogOpen">
    <DialogTrigger as-child>
      <Button>
        <LucidePlus />
        {{ t('administrator.classes.add') }}
      </Button>
    </DialogTrigger>
    <DialogContent>
      <DialogHeader>
        <DialogTitle>{{ t('administrator.classes.add') }}</DialogTitle>
        <DialogDescription>{{ t('common.requiredFieldsInfo') }}</DialogDescription>
      </DialogHeader>
      <ClassAddGeneralForm @submit="onSubmit" :error="error" :is-pending="isPending">
        <DialogFooter>
          <DialogClose as-child>
            <Button variant="outline" type="button" :loading="isPending">{{ t('common.actions.cancel') }}</Button>
          </DialogClose>
          <Button>{{ t('common.actions.save') }}</Button>
        </DialogFooter>
      </ClassAddGeneralForm>
    </DialogContent>
  </Dialog>
</template>
