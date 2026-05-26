<script setup lang="ts">
import type { SchoolUnitValues } from '../../forms/schoolUnits';
import SchoolUnitForm from './SchoolUnitForm.vue';
import { useCreateSchoolUnit } from '@/api/hooks/school-structure/createSchoolUnit';
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

const props = defineProps<{ complexId: number }>();

const { t } = useI18n();
const dialogOpen = ref(false);

const { mutate: create, isPending, error } = useCreateSchoolUnit();

const onSubmit = (values: SchoolUnitValues) =>
  create(
    { ...values, district: values.district ?? null, town: values.town ?? null, flatNumber: values.flatNumber ?? null, schoolComplexId: props.complexId },
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
        {{ t('administrator.schoolStructure.addSchoolUnit') }}
      </Button>
    </DialogTrigger>
    <DialogContent>
      <DialogHeader>
        <DialogTitle>{{ t('administrator.schoolStructure.addSchoolUnit') }}</DialogTitle>
        <DialogDescription>{{ t('common.requiredFieldsInfo') }}</DialogDescription>
      </DialogHeader>
      <SchoolUnitForm @submit="onSubmit" :is-pending="isPending" :error="error">
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
