<script setup lang="ts">
import { childFormSchema, type ChildFormValues } from '../../forms/child';
import { ErrorBanner } from '@/components/ui/banner';
import {
  FormControl,
  FormField,
  FormItem,
  FormLabel,
  FormMessage,
} from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select';
import { useForm } from 'vee-validate';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps<{
  isPending: boolean;
  initialValues?: Partial<ChildFormValues>;
  error: Error | null;
}>();
const emit = defineEmits<{ submit: [ChildFormValues] }>();

const { t } = useI18n();

const form = useForm({
  validationSchema: childFormSchema,
  initialValues: props.initialValues,
});

const showPesel = computed(() => !!props.initialValues?.pesel);
const showAlternateDocument = computed(() => !!props.initialValues?.alternateIdentityDocument);

const onSubmit = form.handleSubmit((values) => emit('submit', values));
</script>

<template>
  <form @submit="onSubmit" class="space-y-3">
    <div class="grid sm:grid-cols-2 gap-3">
      <FormField v-slot="{ componentField }" name="lastName">
        <FormItem>
          <FormLabel required>{{ t('common.data.lastName') }}</FormLabel>
          <FormControl>
            <Input :disabled="isPending" v-bind="componentField" />
          </FormControl>
          <FormMessage />
        </FormItem>
      </FormField>
      <FormField v-slot="{ componentField }" name="firstName">
        <FormItem>
          <FormLabel required>{{ t('common.data.firstName') }}</FormLabel>
          <FormControl>
            <Input :disabled="isPending" v-bind="componentField" />
          </FormControl>
          <FormMessage />
        </FormItem>
      </FormField>
      <FormField v-slot="{ componentField }" name="secondName">
        <FormItem>
          <FormLabel>{{ t('common.data.secondName') }}</FormLabel>
          <FormControl>
            <Input :disabled="isPending" v-bind="componentField" />
          </FormControl>
          <FormMessage />
        </FormItem>
      </FormField>
      <FormField v-slot="{ componentField }" name="gender">
        <FormItem>
          <FormLabel>{{ t('common.data.gender') }}</FormLabel>
          <FormControl>
            <Select v-bind="componentField">
              <SelectTrigger>
                <SelectValue :placeholder="t('common.actions.select')" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="male">{{ t('common.data.male') }}</SelectItem>
                <SelectItem value="female">{{ t('common.data.female') }}</SelectItem>
              </SelectContent>
            </Select>
          </FormControl>
          <FormMessage />
        </FormItem>
      </FormField>
      <FormField v-slot="{ componentField }" name="birthdate">
        <FormItem>
          <FormLabel required>{{ t('common.data.birthDate') }}</FormLabel>
          <FormControl>
            <Input type="date" :disabled="isPending" v-bind="componentField" />
          </FormControl>
          <FormMessage />
        </FormItem>
      </FormField>
      <FormField v-slot="{ componentField }" name="birthplace">
        <FormItem>
          <FormLabel required>{{ t('common.data.birthplace') }}</FormLabel>
          <FormControl>
            <Input :disabled="isPending" v-bind="componentField" />
          </FormControl>
          <FormMessage />
        </FormItem>
      </FormField>
    </div>

    <div v-if="showPesel || showAlternateDocument" class="grid sm:grid-cols-2 gap-3">
      <FormField v-if="showPesel" v-slot="{ componentField }" name="pesel">
        <FormItem>
          <FormLabel>{{ t('common.data.pesel') }}</FormLabel>
          <FormControl>
            <Input disabled v-bind="componentField" />
          </FormControl>
          <FormMessage />
        </FormItem>
      </FormField>
      <FormField v-if="showAlternateDocument" v-slot="{ componentField }" name="alternateIdentityDocument">
        <FormItem>
          <FormLabel>{{ t('common.data.alternateDocument') }}</FormLabel>
          <FormControl>
            <Input disabled v-bind="componentField" />
          </FormControl>
          <FormMessage />
        </FormItem>
      </FormField>
    </div>

    <fieldset>
      <legend class="text-sm leading-none font-medium mb-3">
        {{ t('common.data.address') }}
      </legend>
      <div class="grid sm:grid-cols-2 gap-3">
        <FormField v-slot="{ componentField }" name="residenceAddressCountry">
          <FormItem>
            <FormLabel required>{{ t('common.data.country') }}</FormLabel>
            <FormControl>
              <Input :disabled="isPending" v-bind="componentField" />
            </FormControl>
            <FormMessage />
          </FormItem>
        </FormField>
        <FormField v-slot="{ componentField }" name="residenceAddressCommune">
          <FormItem>
            <FormLabel>{{ t('common.data.commune') }}</FormLabel>
            <FormControl>
              <Input :disabled="isPending" v-bind="componentField" />
            </FormControl>
            <FormMessage />
          </FormItem>
        </FormField>
        <FormField v-slot="{ componentField }" name="residenceAddressTown">
          <FormItem>
            <FormLabel>{{ t('common.data.town') }}</FormLabel>
            <FormControl>
              <Input :disabled="isPending" v-bind="componentField" />
            </FormControl>
            <FormMessage />
          </FormItem>
        </FormField>
        <FormField v-slot="{ componentField }" name="residenceAddressPostalCode">
          <FormItem>
            <FormLabel>{{ t('common.data.postalCode') }}</FormLabel>
            <FormControl>
              <Input :disabled="isPending" v-bind="componentField" />
            </FormControl>
            <FormMessage />
          </FormItem>
        </FormField>
        <FormField v-slot="{ componentField }" name="residenceAddressStreet">
          <FormItem>
            <FormLabel>{{ t('common.data.street') }}</FormLabel>
            <FormControl>
              <Input :disabled="isPending" v-bind="componentField" />
            </FormControl>
            <FormMessage />
          </FormItem>
        </FormField>
        <FormField v-slot="{ componentField }" name="residenceAddressHouseNumber">
          <FormItem>
            <FormLabel>{{ t('common.data.houseNumber') }}</FormLabel>
            <FormControl>
              <Input :disabled="isPending" v-bind="componentField" />
            </FormControl>
            <FormMessage />
          </FormItem>
        </FormField>
        <FormField v-slot="{ componentField }" name="residenceAddressFlatNumber">
          <FormItem>
            <FormLabel>{{ t('common.data.flatNumber') }}</FormLabel>
            <FormControl>
              <Input :disabled="isPending" v-bind="componentField" />
            </FormControl>
            <FormMessage />
          </FormItem>
        </FormField>
      </div>
    </fieldset>

    <ErrorBanner v-if="error" :error="error" />

    <slot />
  </form>
</template>
