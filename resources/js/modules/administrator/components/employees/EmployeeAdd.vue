<script setup lang="ts">
import type { EmployeeValues } from '../../forms/employee';
import EmployeeForm from './EmployeeForm.vue';
import { useAddEmployee } from '@/api/hooks/employee/addEmployee';
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

const { mutate: add, isPending, error } = useAddEmployee();

const onSubmit = (values: EmployeeValues) =>
  add(
    {
      ...values,
      shortcut: values.shortcut ?? null,
      isAdmin: !!values.roles.find((role) => role === 'admin'),
      isHeadmaster: !!values.roles.find((role) => role === 'headmaster'),
      isSecretary: !!values.roles.find((role) => role === 'secretary'),
      isTeacher: !!values.roles.find((role) => role === 'teacher'),
    },
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
      <Button>
        <LucidePlus />
        {{ t('administrator.employees.add') }}
      </Button>
    </DialogTrigger>
    <DialogContent>
      <DialogHeader>
        <DialogTitle>{{ t('administrator.employees.add') }}</DialogTitle>
        <DialogDescription>{{ t('common.requiredFieldsInfo') }}</DialogDescription>
      </DialogHeader>
      <EmployeeForm @submit="onSubmit" :is-pending="isPending" :error="error">
        <DialogFooter>
          <DialogClose as-child>
            <Button variant="outline" type="button">{{ t('common.actions.cancel') }}</Button>
          </DialogClose>
          <Button :loading="isPending">{{ t('common.actions.save') }}</Button>
        </DialogFooter>
      </EmployeeForm>
    </DialogContent>
  </Dialog>
</template>
