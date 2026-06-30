<script setup lang="ts">
import { setUnitActivityForm } from '../../forms/setUnitActivity';
import { useToggleSchoolUnitActivity } from '@/api/hooks/school-structure/toggleSchoolUnitActivity';
import { ErrorBanner } from '@/components/ui/banner';
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
import { FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form';
import { PasswordInput } from '@/components/ui/input';
import { LucideArchive } from 'lucide-vue-next';
import { useForm } from 'vee-validate';
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps<{ unitId: number; state: boolean }>();

const { t } = useI18n();
const dialogOpen = ref(false);

const { mutate: setActivity, isPending, error } = useToggleSchoolUnitActivity();

const form = useForm({
  validationSchema: setUnitActivityForm,
});

const onSubmit = form.handleSubmit((values) =>
  setActivity(
    { id: props.unitId, password: values.password },
    {
      onSuccess: () => {
        dialogOpen.value = false;
      },
    },
  ),
);
</script>

<template>
  <Dialog v-model:open="dialogOpen">
    <DialogTrigger as-child>
      <Button variant="outline">
        <LucideArchive />
        {{ state ? t('common.actions.archive') : t('common.actions.unarchive') }}
      </Button>
    </DialogTrigger>
    <DialogContent>
      <DialogHeader>
        <DialogTitle>{{
          state
            ? t('administrator.schoolStructure.archiveSchoolUnit.title')
            : t('administrator.schoolStructure.unarchiveSchoolUnit.title')
        }}</DialogTitle>
        <DialogDescription>
          {{
            state
              ? t('administrator.schoolStructure.archiveSchoolUnit.description')
              : t('administrator.schoolStructure.unarchiveSchoolUnit.description')
          }}
        </DialogDescription>
      </DialogHeader>

      <form @submit="onSubmit" class="space-y-3">
        <FormField name="password" v-slot="{ componentField }">
          <FormItem>
            <FormLabel>{{ t('common.data.password') }}</FormLabel>
            <FormControl>
              <PasswordInput v-bind="componentField" :disabled="isPending" />
            </FormControl>
            <FormMessage />
          </FormItem>
        </FormField>

        <ErrorBanner v-if="error" :error="error" />

        <DialogFooter>
          <DialogClose as-child>
            <DialogClose as-child>
              <Button variant="outline" type="button">{{ t('common.actions.cancel') }}</Button>
            </DialogClose>
            <Button :loading="isPending">{{ t('common.actions.confirm') }}</Button>
          </DialogClose>
        </DialogFooter>
      </form>
    </DialogContent>
  </Dialog>
</template>
