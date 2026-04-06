<script setup lang="ts">
import { changeEmailSchema } from '../forms/changeEmail';
import { useChangeEmail } from '@/api/hooks/user/changeEmail';
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
import { Input, PasswordInput } from '@/components/ui/input';
import { useForm } from 'vee-validate';
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const dialogOpen = ref(false);
const form = useForm({ validationSchema: changeEmailSchema });

const { mutate: changeEmail, isPending, error } = useChangeEmail();

const onSubmit = form.handleSubmit(async (values) =>
  changeEmail(values, {
    onSuccess: () => {
      dialogOpen.value = false;
    },
  }),
);
</script>

<template>
  <Dialog v-model:open="dialogOpen">
    <DialogTrigger as-child>
      <Button size="sm" variant="secondary">{{ t('common.actions.change') }}</Button>
    </DialogTrigger>
    <DialogContent>
      <form @submit="onSubmit" class="space-y-3">
        <DialogHeader>
          <DialogTitle>{{ t('myAccount.accountData.changeEmail.title') }}</DialogTitle>
          <DialogDescription>{{
            t('myAccount.accountData.changeEmail.description')
          }}</DialogDescription>
        </DialogHeader>
        <FormField v-slot="{ componentField }" name="email">
          <FormItem>
            <FormLabel>{{ t('common.data.email') }}</FormLabel>
            <FormControl>
              <Input
                type="text"
                v-bind="componentField"
                :disabled="isPending"
                autocapitalize="off"
              />
            </FormControl>
            <FormMessage />
          </FormItem>
        </FormField>
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
          <Button type="submit" :loading="isPending">{{ t('common.actions.confirm') }}</Button>
        </DialogFooter>
      </form>
    </DialogContent>
  </Dialog>
</template>
