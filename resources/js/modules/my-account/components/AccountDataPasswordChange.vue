<script setup lang="ts">
import { useChangePassword } from '@/api/hooks/user/changePassword';
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
import { changePasswordSchema } from '@/modules/authentication/forms/changePassword';
import { useForm } from 'vee-validate';
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const dialogOpen = ref(false);
const form = useForm({ validationSchema: changePasswordSchema });

const { mutate: changePassword, isPending, error } = useChangePassword();

const onSubmit = form.handleSubmit(async (values) =>
  changePassword(values, {
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
          <DialogTitle>{{ t('myAccount.accountData.changePassword.title') }}</DialogTitle>
          <DialogDescription>{{
            t('myAccount.accountData.changePassword.description')
          }}</DialogDescription>
        </DialogHeader>
        <FormField v-slot="{ componentField }" name="oldPassword">
          <FormItem>
            <FormLabel>{{ t('myAccount.accountData.changePassword.oldPassword') }}</FormLabel>
            <FormControl>
              <PasswordInput v-bind="componentField" :disabled="isPending" />
            </FormControl>
            <FormMessage />
          </FormItem>
        </FormField>
        <FormField v-slot="{ componentField }" name="newPassword">
          <FormItem>
            <FormLabel>{{ t('myAccount.accountData.changePassword.newPassword') }}</FormLabel>
            <FormControl>
              <PasswordInput v-bind="componentField" :disabled="isPending" />
            </FormControl>
            <FormMessage />
          </FormItem>
        </FormField>
        <FormField v-slot="{ componentField }" name="repeatPassword">
          <FormItem>
            <FormLabel>{{ t('common.repeatPassword') }}</FormLabel>
            <FormControl>
              <PasswordInput v-bind="componentField" :disabled="isPending" />
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
