<script setup lang="ts">
import { useLogIn } from '@/api/hooks/session/logIn';
import { ErrorBanner } from '@/components/ui/banner';
import { Button } from '@/components/ui/button';
import { FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form';
import { Input, PasswordInput } from '@/components/ui/input';
import ActivationBanner from '@/modules/authentication/components/ActivationBanner.vue';
import FormHeader from '@/modules/authentication/components/FormHeader.vue';
import { logInSchema } from '@/modules/authentication/forms/logIn';
import { useForm } from 'vee-validate';
import { useI18n } from 'vue-i18n';
import { useRouter } from 'vue-router';

const { t } = useI18n();
const router = useRouter();

const form = useForm({
  validationSchema: logInSchema,
});

const { mutate: logIn, isPending, error } = useLogIn();

const onSubmit = form.handleSubmit((values) =>
  logIn(values, {
    onSuccess: async () => {
      await router.push({ name: 'myAccount' });
    },
  }),
);
</script>

<template>
  <div>
    <FormHeader :title="t('auth.logIn.title')" :subtitle="t('auth.logIn.description')" />

    <form @submit="onSubmit" class="space-y-6 mt-5">
      <FormField v-slot="{ componentField }" name="email">
        <FormItem>
          <FormLabel>{{ t('common.data.email') }}</FormLabel>
          <FormControl>
            <Input type="text" v-bind="componentField" :disabled="isPending" autocapitalize="off" />
          </FormControl>
          <FormMessage />
        </FormItem>
      </FormField>
      <FormField v-slot="{ componentField }" name="password">
        <FormItem>
          <div class="flex justify-between">
            <FormLabel>{{ t('common.data.password') }}</FormLabel>
          </div>
          <FormControl>
            <PasswordInput v-bind="componentField" :disabled="isPending" />
          </FormControl>
          <FormMessage />
        </FormItem>
      </FormField>
      <ErrorBanner :error="error" v-if="error" />
      <Button type="submit" class="w-full" :loading="isPending">
        {{ t('auth.logIn.submit') }}
      </Button>
    </form>
    <ActivationBanner class="mt-10" />
  </div>
</template>
