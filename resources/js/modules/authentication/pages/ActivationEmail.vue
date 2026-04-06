<script setup lang="ts">
import { useActivationStore } from '@/stores/activation';
import BackButton from '@/modules/authentication/components/BackButton.vue';
import FormHeader from '@/modules/authentication/components/FormHeader.vue';
import { checkEmailAvailabilitySchema } from '@/modules/authentication/forms/checkEmailAvailability';
import { checkEmailAvailbility } from '@/api/hooks/activation/checkEmailAvailability';
import { ErrorBanner } from '@/components/ui/banner';
import { Button } from '@/components/ui/button';
import { FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import { useForm } from 'vee-validate';
import { useI18n } from 'vue-i18n';
import { useRouter } from 'vue-router';

const { t } = useI18n();
const router = useRouter();
const activationStore = useActivationStore();

const form = useForm({
  validationSchema: checkEmailAvailabilitySchema,
  initialValues: {
    email: 'email' in activationStore.status ? activationStore.status.email : undefined,
  },
});

const { mutate: checkAvailbility, isPending, error } = checkEmailAvailbility();

const onSubmit = form.handleSubmit(async (values) => {
  if ('email' in activationStore.status && activationStore.status.email === values.email)
    return await router.push({
      name:
        activationStore.status.step === 'attach_to_account'
          ? 'auth.activation.logIn'
          : 'auth.activation.setPassword',
    });

  if (activationStore.status.step === 'not_started') return;

  checkAvailbility(values.email, {
    onSuccess: async (data) => {
      if (activationStore.status.step === 'not_started') return;

      activationStore.status = {
        step: data.available ? 'email_available' : 'attach_to_account',
        code: activationStore.status.code,
        email: values.email,
      };
      console.log(activationStore.status);
      await router.push({
        name: data.available ? 'auth.activation.setPassword' : 'auth.activation.logIn',
      });
    },
  });
});
</script>

<template>
  <div>
    <FormHeader
      :title="t('auth.activation.title')"
      :subtitle="t('auth.activation.stepDescriptions.email')"
    />
    <form @submit.prevent="onSubmit()" class="space-y-6 mt-5">
      <FormField v-slot="{ componentField }" name="email">
        <FormItem>
          <FormLabel>{{ t('common.data.email') }}</FormLabel>
          <FormControl>
            <Input type="text" v-bind="componentField" :disabled="isPending" autocapitalize="off" />
          </FormControl>
          <FormMessage />
        </FormItem>
      </FormField>

      <p class="text-right">
        <BackButton route-name="auth.activation.code" />
      </p>

      <ErrorBanner :error="error" v-if="error" />
      <Button type="submit" class="w-full" :disabled="isPending" :loading="isPending">
        {{ t('common.actions.back') }}
      </Button>
    </form>
  </div>
</template>
