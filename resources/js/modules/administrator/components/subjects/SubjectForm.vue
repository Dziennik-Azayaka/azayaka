<script setup lang="ts">
import { subjectFormSchema, type SubjectValues } from '../../forms/subject';
import { ErrorBanner } from '@/components/ui/banner';
import { FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import { useForm } from 'vee-validate';
import { useI18n } from 'vue-i18n';

const props = defineProps<{
  isPending: boolean;
  initialValues?: SubjectValues;
  error: Error | null;
}>();
const emit = defineEmits<{ submit: [SubjectValues] }>();

const { t } = useI18n();

const form = useForm({
  validationSchema: subjectFormSchema,
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
          <Input :disabled="isPending" v-bind="componentField" />
        </FormControl>
        <FormMessage />
      </FormItem>
    </FormField>
    <FormField v-slot="{ componentField }" name="shortcut">
      <FormItem>
        <FormLabel required>{{ t('common.data.short') }}</FormLabel>
        <FormControl>
          <Input :disabled="isPending" v-bind="componentField" />
        </FormControl>
        <FormMessage />
      </FormItem>
    </FormField>

    <ErrorBanner v-if="error" :error="error" />

    <slot />
  </form>
</template>
