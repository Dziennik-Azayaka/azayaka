<script setup lang="ts">
import { setUnitActivityForm } from '../../forms/setUnitActivity';
import { useSetEmployeeActivity } from '@/api/hooks/employee/setEmployeeActivity';
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

const props = defineProps<{ employeeId: number; state: boolean }>();

const { t } = useI18n();
const dialogOpen = ref(false);

const { mutate: setActivity, isPending, error } = useSetEmployeeActivity();

const form = useForm({
  validationSchema: setUnitActivityForm,
});

const onSubmit = form.handleSubmit((values) =>
  setActivity(
    { id: props.employeeId, state: !props.state, password: values.password },
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
      <Button variant="destructive">
        <LucideArchive />
        {{ state ? t('common.actions.archive') : t('common.actions.unarchive') }}
      </Button>
    </DialogTrigger>
    <DialogContent>
      <DialogHeader>
        <DialogTitle>{{
          state
            ? t('administrator.employees.archive.title')
            : t('administrator.employees.unarchive.title')
        }}</DialogTitle>
        <DialogDescription>
          {{
            state
              ? t('administrator.employees.archive.description')
              : t('administrator.employees.unarchive.description')
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
