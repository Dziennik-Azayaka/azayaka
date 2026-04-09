<script setup lang="ts">
import type { EmployeeValues } from '../../forms/employee';
import EmployeeArchive from './EmployeeArchive.vue';
import EmployeeForm from './EmployeeForm.vue';
import { useEditEmployee } from '@/api/hooks/employee/editEmployee';
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
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps<{
  employeeId: number;
  initialValues: EmployeeValues;
  isActive: boolean;
}>();

const { t } = useI18n();
const dialogOpen = ref(false);

const { mutate: edit, isPending, error } = useEditEmployee();

const onSubmit = (values: EmployeeValues) =>
  edit(
    {
      id: props.employeeId,
      body: {
        ...values,
        shortcut: values.shortcut ?? null,
        isAdmin: !!values.roles.find((role) => role === 'admin'),
        isHeadmaster: !!values.roles.find((role) => role === 'headmaster'),
        isSecretary: !!values.roles.find((role) => role === 'secretary'),
        isTeacher: !!values.roles.find((role) => role === 'teacher'),
      },
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
      <slot />
    </DialogTrigger>
    <DialogContent>
      <DialogHeader>
        <DialogTitle>{{ t('administrator.employees.edit') }}</DialogTitle>
        <DialogDescription>{{ t('common.requiredFieldsInfo') }}</DialogDescription>
      </DialogHeader>
      <EmployeeForm
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
          <EmployeeArchive :employee-id="employeeId" :state="isActive" />
          <Button :loading="isPending">{{ t('common.actions.save') }}</Button>
        </DialogFooter>
      </EmployeeForm>
    </DialogContent>
  </Dialog>
</template>
