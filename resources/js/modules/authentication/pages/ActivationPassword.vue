<script setup lang="ts">
import { useActivationStore } from '@/stores/activation';
import BackButton from '@/modules/authentication/components/BackButton.vue';
import FormHeader from '@/modules/authentication/components/FormHeader.vue';
import { setPasswordSchema } from '../forms/setPassword';
import { useActivateAccess } from '@/api/hooks/activation/activateAccess';
import { ErrorBanner } from '@/components/ui/banner';
import { Button } from '@/components/ui/button';
import { FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form';
import { PasswordInput } from '@/components/ui/input';
import { useForm } from 'vee-validate';
import { useI18n } from 'vue-i18n';
import { useRouter } from 'vue-router';

const { t } = useI18n();
const router = useRouter();
const activationStore = useActivationStore();

const form = useForm({
  validationSchema: setPasswordSchema,
});

const { mutate: activate, isPending, error } = useActivateAccess();

const onSubmit = form.handleSubmit(async (values) => {
  if (activationStore.status.step !== 'email_available') return;

  activate(
    {
      words: activationStore.status.code.split(','),
      email: activationStore.status.email,
      password: values.password,
    },
    { onSuccess: () => router.push({ name: 'myAccount' }) },
  );
});
</script>

<template>
  <div>
    <FormHeader
      :title="t('auth.activation.title')"
      :subtitle="t('auth.activation.stepDescriptions.setPassword')"
    />
    <form @submit.prevent="onSubmit()" class="space-y-6 mt-5">
      <FormField v-slot="{ componentField }" name="password">
        <FormItem>
          <FormLabel>{{ t('common.data.password') }}</FormLabel>
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

      <ErrorBanner :error="error" v-if="error" />

      <p class="text-right">
        <BackButton route-name="auth.activation.email" />
      </p>

      <Button type="submit" class="w-full" :loading="isPending">
        {{ t('common.actions.next') }}
      </Button>
    </form>
  </div>
</template>
