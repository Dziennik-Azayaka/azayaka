<script setup lang="ts">
import { useCheckCode } from '@/api/hooks/activation/checkCode';
import { ErrorBanner } from '@/components/ui/banner';
import { Button } from '@/components/ui/button';
import { FormControl, FormField, FormItem, FormMessage } from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import BackButton from '@/modules/authentication/components/BackButton.vue';
import FormHeader from '@/modules/authentication/components/FormHeader.vue';
import { checkCodeSchema } from '@/modules/authentication/forms/checkCode';
import { useActivationStore } from '@/stores/activation';
import { useForm } from 'vee-validate';
import { useI18n } from 'vue-i18n';
import { useRouter } from 'vue-router';

const { t } = useI18n();
const router = useRouter();
const activationStore = useActivationStore();
const form = useForm({
  validationSchema: checkCodeSchema,
  initialValues: { words: Array(10).fill('') },
});
const { mutate: checkCode, isPending, error } = useCheckCode();

const onSubmit = form.handleSubmit(async (values) => {
  if (
    activationStore.status.step !== 'not_started' &&
    activationStore.status.code === values.words.join(',')
  )
    return await router.push({ name: 'auth.activation.email' });

  await checkCode(values.words, {
    onSuccess: async () => await router.push({ name: 'auth.activation.email' }),
  });
});
</script>

<template>
  <div>
    <FormHeader
      :title="t('auth.activation.title')"
      :subtitle="t('auth.activation.stepDescriptions.code')"
    />
    <form @submit="onSubmit" class="space-y-6 mt-5">
      <ol class="list-decimal grid sm:grid-cols-2 gap-y-4 gap-x-6 marker:leading-9">
        <FormField v-for="i in 10" :key="i" v-slot="{ componentField }" :name="`words[${i - 1}]`">
          <FormItem>
            <li class="pl-2 ml-6">
              <FormControl>
                <Input
                  type="text"
                  :disabled="isPending"
                  v-bind="componentField"
                  autocapitalize="off"
                />
              </FormControl>
              <FormMessage />
            </li>
          </FormItem>
        </FormField>
      </ol>

      <p class="text-end">
        <BackButton route-name="auth.logIn" />
      </p>

      <ErrorBanner :error="error" v-if="error" />
      <Button type="submit" class="w-full" :loading="isPending">
        {{ t('common.actions.next') }}
      </Button>
    </form>
  </div>
</template>
