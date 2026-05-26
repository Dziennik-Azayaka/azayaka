<script setup lang="ts">
import type { SchoolUnitValues } from '../../forms/schoolUnits';
import SchoolUnitForm from './SchoolUnitForm.vue';
import { useEditSchoolUnit } from '@/api/hooks/school-structure/editSchoolUnit';
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
import { LucidePencil } from 'lucide-vue-next';
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps<{ unitId: number; complexId: number; initialValues: SchoolUnitValues }>();

const { t } = useI18n();
const dialogOpen = ref(false);

const { mutate: edit, isPending, error } = useEditSchoolUnit();

const onSubmit = (values: SchoolUnitValues) =>
  edit(
    {
      id: props.unitId,
      body: { ...values, district: values.district ?? null, town: values.town ?? null, flatNumber: values.flatNumber ?? null, schoolComplexId: props.complexId },
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
      <Button variant="outline">
        <LucidePencil />
        {{ t('common.actions.editData') }}
      </Button>
    </DialogTrigger>
    <DialogContent>
      <DialogHeader>
        <DialogTitle>{{ t('administrator.schoolStructure.editSchoolUnit') }}</DialogTitle>
        <DialogDescription>{{ t('common.requiredFieldsInfo') }}</DialogDescription>
      </DialogHeader>
      <SchoolUnitForm
        @submit="onSubmit"
        :is-pending="isPending"
        :error="error"
        :initial-values="initialValues"
      >
        <DialogFooter>
          <DialogClose as-child>
            <Button variant="outline" type="button">{{ t('common.actions.cancel') }}</Button>
          </DialogClose>
          <Button :loading="isPending">{{ t('common.actions.save') }}</Button>
        </DialogFooter>
      </SchoolUnitForm>
    </DialogContent>
  </Dialog>
</template>
