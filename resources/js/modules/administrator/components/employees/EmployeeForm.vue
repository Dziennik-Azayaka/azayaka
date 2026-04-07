<script setup lang="ts">
import { employeeFormSchema, type EmployeeValues } from '../../forms/employee';
import { ErrorBanner } from '@/components/ui/banner';
import { Checkbox } from '@/components/ui/checkbox';
import { FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import { useForm } from 'vee-validate';
import { useI18n } from 'vue-i18n';

const props = defineProps<{
  isPending: boolean;
  initialValues?: EmployeeValues;
  error: Error | null;
}>();
const emit = defineEmits<{ submit: [EmployeeValues] }>();

const { t } = useI18n();

const form = useForm({
  validationSchema: employeeFormSchema,
  initialValues: props.initialValues ?? { roles: [] },
});

const roles = new Set<string>(['teacher', 'secretary', 'headmaster', 'administrator']);
function handleRoleCheck(
  checked: boolean,
  role: string,
  values: string[],
  callback: (roles: string[]) => void,
) {
  const newValue = checked ? [...values, role] : values.filter((v: string) => v !== role);
  callback(newValue);
}

const onSubmit = form.handleSubmit((values) => emit('submit', values));
</script>

<template>
  <form @submit="onSubmit" class="space-y-3">
    <div class="grid sm:grid-cols-5 gap-3">
      <FormField v-slot="{ componentField }" name="lastName">
        <FormItem class="sm:col-start-1 sm:col-end-3 h-min">
          <FormLabel required>{{ t('common.data.lastName') }}</FormLabel>
          <FormControl>
            <Input :disabled="isPending" v-bind="componentField" />
          </FormControl>
          <FormMessage />
        </FormItem>
      </FormField>
      <FormField v-slot="{ componentField }" name="firstName">
        <FormItem class="sm:col-start-3 sm:col-end-5 h-min">
          <FormLabel required>{{ t('common.data.firstName') }}</FormLabel>
          <FormControl>
            <Input :disabled="isPending" v-bind="componentField" />
          </FormControl>
          <FormMessage />
        </FormItem>
      </FormField>
      <FormField v-slot="{ componentField }" name="shortcut">
        <FormItem class="h-min">
          <FormLabel>{{ t('common.data.short') }}</FormLabel>
          <FormControl>
            <Input :disabled="isPending" v-bind="componentField" :placeholder="t('common.auto')" />
          </FormControl>
          <FormMessage />
        </FormItem>
      </FormField>
    </div>
    <FormField v-slot="{ value, handleChange }" name="roles" as="fieldset">
      <FormItem>
        <legend
          class="flex items-center gap-0.5 text-sm leading-none font-medium select-none group-data-[disabled=true]:pointer-events-none group-data-[disabled=true]:opacity-50 peer-disabled:cursor-not-allowed peer-disabled:opacity-50 data-[error=true]:text-destructive-foreground"
        >
          {{ t('common.data.roles') }} <span class="text-destructive">*</span>
        </legend>
        <div class="grid sm:grid-cols-1 gap-3 mt-2">
          <FormItem v-for="role in roles" :key="role" class="flex flex-row">
            <FormControl>
              <Checkbox
                :model-value="value?.includes(role)"
                @update:model-value="
                  (checked) => handleRoleCheck(!!checked, role, value, handleChange)
                "
              />
            </FormControl>
            <FormLabel class="font-normal text-sm">{{
              t(`administrator.employees.roles.${role}`)
            }}</FormLabel>
          </FormItem>
        </div>
        <FormMessage />
      </FormItem>
    </FormField>

    <ErrorBanner v-if="error" :error="error" />

    <slot />
  </form>
</template>
