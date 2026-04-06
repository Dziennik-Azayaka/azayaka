<script setup lang="ts">
import { schoolComplexFormSchema, type SchoolComplexValues } from '../../forms/schoolComplex';
import { ErrorBanner } from '@/components/ui/banner';
import { FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import { useForm } from 'vee-validate';
import { useI18n } from 'vue-i18n';

const props = defineProps<{
  isPending: boolean;
  initialValues?: SchoolComplexValues;
  error: Error | null;
}>();
const emit = defineEmits<{ submit: [SchoolComplexValues] }>();

const { t } = useI18n();

const form = useForm({
  validationSchema: schoolComplexFormSchema,
  initialValues: props.initialValues,
});

const onSubmit = form.handleSubmit((values) => emit('submit', values));
</script>

<template>
  <form @submit="onSubmit" class="space-y-3">
    <FormField v-slot="{ componentField }" name="name">
      <FormItem>
        <FormLabel required>{{ t('common.data.name') }}</FormLabel>
        <FormControl>
          <Input v-bind="componentField" :disabled="isPending" />
        </FormControl>
        <FormMessage />
      </FormItem>
    </FormField>

    <ErrorBanner v-if="error" :error="error" />

    <slot />
  </form>
</template>
