<script setup lang="ts">
import { removeSessionSchema } from '../forms/removeSession';
import { useRemoveSession } from '@/api/hooks/session/removeSession';
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
import { useForm } from 'vee-validate';
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps<{ sessionId: string }>();

const { t } = useI18n();

const dialogOpen = ref(false);
const form = useForm({ validationSchema: removeSessionSchema });

const { mutate: removeSession, isPending, error } = useRemoveSession();

const onSubmit = form.handleSubmit((values) =>
  removeSession(
    { id: props.sessionId, password: values.password },
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
      <Button size="sm" variant="secondary">{{ t('myAccount.home.logOutSession.title') }}</Button>
    </DialogTrigger>
    <DialogContent>
      <form @submit="onSubmit" class="space-y-3">
        <DialogHeader>
          <DialogTitle>{{ t('myAccount.home.logOutSession.title') }}</DialogTitle>
          <DialogDescription>{{ t('myAccount.home.logOutSession.description') }}</DialogDescription>
        </DialogHeader>
        <FormField v-slot="{ componentField }" name="password">
          <FormItem>
            <FormLabel>{{ t('common.data.password') }}</FormLabel>
            <FormControl>
              <PasswordInput v-bind="componentField" :disabled="isPending" autocapitalize="off" />
            </FormControl>
            <FormMessage />
          </FormItem>
        </FormField>
        <ErrorBanner v-if="error" :error="error" />
        <DialogFooter>
          <DialogClose as-child>
            <Button variant="outline" type="button">{{ t('common.actions.cancel') }}</Button>
          </DialogClose>
          <Button type="submit" :loading="isPending">{{ t('common.actions.confirm') }} </Button>
        </DialogFooter>
      </form>
    </DialogContent>
  </Dialog>
</template>
