<script setup lang="ts">
import type { SchoolComplexValues } from '../../forms/schoolComplex';
import SchoolComplexForm from './SchoolComplexForm.vue';
import { useCreateSchoolComplex } from '@/api/hooks/school-structure/createSchoolComplex';
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
import { LucidePlus } from 'lucide-vue-next';
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
const dialogOpen = ref(false);

const { mutate: create, isPending, error } = useCreateSchoolComplex();

const onSubmit = (values: SchoolComplexValues) =>
  create(values.name, {
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
        {{ t('administrator.schoolStructure.createSchoolComplex') }}
      </Button>
    </DialogTrigger>
    <DialogContent>
      <DialogHeader>
        <DialogTitle>{{ t('administrator.schoolStructure.createSchoolComplex') }}</DialogTitle>
      </DialogHeader>
      <SchoolComplexForm @submit="onSubmit" :is-pending="isPending" :error="error">
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
