<script setup lang="ts">
import type { SchoolComplexValues } from '../../forms/schoolComplex';
import SchoolComplexForm from './SchoolComplexForm.vue';
import { useEditSchoolComplex } from '@/api/hooks/school-structure/editSchoolComplex';
import { Button } from '@/components/ui/button';
import {
  Dialog,
  DialogClose,
  DialogContent,
  DialogFooter,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from '@/components/ui/dialog';
import { LucidePencil } from 'lucide-vue-next';
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps<{ complexId: number; initialValues: SchoolComplexValues }>();

const { t } = useI18n();
const dialogOpen = ref(false);

const { mutate: edit, isPending, error } = useEditSchoolComplex();

const onSubmit = (values: SchoolComplexValues) =>
  edit(
    { ...values, id: props.complexId },
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
      <Button variant="secondary">
        <LucidePencil />
        {{ t('common.actions.editData') }}
      </Button>
    </DialogTrigger>
    <DialogContent>
      <DialogHeader>
        <DialogTitle>{{ t('administrator.schoolStructure.editSchoolComplex') }}</DialogTitle>
      </DialogHeader>
      <SchoolComplexForm
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
      </SchoolComplexForm>
    </DialogContent>
  </Dialog>
</template>
