<script setup lang="ts">
import { schoolUnitFormSchema, type SchoolUnitValues } from '../../forms/schoolUnits';
import InstitutionTypes from '@/assets/json/institution-types.json';
import StudentCategories from '@/assets/json/student-categories.json';
import Voivodeships from '@/assets/json/voivodeships.json';
import { ErrorBanner } from '@/components/ui/banner';
import { FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select';
import { Separator } from '@/components/ui/separator';
import { useForm } from 'vee-validate';
import { useI18n } from 'vue-i18n';

const props = defineProps<{
  isPending: boolean;
  initialValues?: SchoolUnitValues;
  error: Error | null;
}>();
const emit = defineEmits<{ submit: [SchoolUnitValues] }>();

const { t } = useI18n();

const form = useForm({
  validationSchema: schoolUnitFormSchema,
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

    <div class="grid sm:grid-cols-2 gap-3">
      <FormField v-slot="{ componentField }" name="shortName">
        <FormItem>
          <FormLabel required>{{ t('common.data.short') }}</FormLabel>
          <FormControl>
            <Input v-bind="componentField" :disabled="isPending" />
          </FormControl>
          <FormMessage />
        </FormItem>
      </FormField>
      <FormField v-slot="{ componentField }" name="type">
        <FormItem class="grid-cols-[1fr]">
          <FormLabel required>
            {{ t('administrator.schoolStructure.data.institutionType') }}
          </FormLabel>
          <FormControl>
            <Select v-bind="componentField" :disabled="isPending" class="w-max">
              <SelectTrigger class="w-full">
                <SelectValue
                  :placeholder="t('common.actions.select')"
                  class="overflow-hidden w-max text-ellipsis"
                />
              </SelectTrigger>
              <SelectContent>
                <SelectItem
                  v-for="{ nameTranslationId, id } in InstitutionTypes"
                  :key="id"
                  :value="id"
                >
                  {{ t(nameTranslationId) }}
                </SelectItem>
              </SelectContent>
            </Select>
          </FormControl>
          <FormMessage />
        </FormItem>
      </FormField>
      <FormField v-slot="{ componentField }" name="studentCategory">
        <FormItem>
          <FormLabel required>
            {{ t('administrator.schoolStructure.data.studentCategory') }}
          </FormLabel>
          <FormControl>
            <Select v-bind="componentField" :disabled="isPending">
              <SelectTrigger class="w-full">
                <SelectValue :placeholder="t('common.actions.select')" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem
                  v-for="{ nameTranslationId, value } in StudentCategories"
                  :key="value"
                  :value="value"
                >
                  {{ t(nameTranslationId) }}
                </SelectItem>
              </SelectContent>
            </Select>
          </FormControl>
          <FormMessage />
        </FormItem>
      </FormField>
    </div>

    <Separator />

    <div class="grid sm:grid-cols-2 gap-3">
      <FormField v-slot="{ componentField }" name="voivodeship">
        <FormItem>
          <FormLabel required>{{ t('common.data.voivodeship') }}</FormLabel>
          <FormControl>
            <Select v-bind="componentField" :disabled="isPending">
              <SelectTrigger class="w-full">
                <SelectValue :placeholder="t('common.actions.select')" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem v-for="{ id, name } in Voivodeships" :key="id" :value="id">
                  {{ name }}
                </SelectItem>
              </SelectContent>
            </Select>
          </FormControl>
          <FormMessage />
        </FormItem>
      </FormField>
      <FormField v-slot="{ componentField }" name="municipality">
        <FormItem>
          <FormLabel required>{{ t('common.data.municipality') }}</FormLabel>
          <FormControl>
            <Input v-bind="componentField" :disabled="isPending" />
          </FormControl>
          <FormMessage />
        </FormItem>
      </FormField>
      <FormField v-slot="{ componentField }" name="postalCode">
        <FormItem>
          <FormLabel required>{{ t('common.data.addressData.postalCode') }}</FormLabel>
          <FormControl>
            <Input v-bind="componentField" :disabled="isPending" />
          </FormControl>
          <FormMessage />
        </FormItem>
      </FormField>
      <FormField v-slot="{ componentField }" name="post">
        <FormItem>
          <FormLabel required>{{ t('common.data.addressData.post') }}</FormLabel>
          <FormControl>
            <Input v-bind="componentField" :disabled="isPending" />
          </FormControl>
          <FormMessage />
        </FormItem>
      </FormField>
            <FormField v-slot="{ componentField }" name="town">
        <FormItem>
          <FormLabel required>{{ t('common.data.addressData.town') }}</FormLabel>
          <FormControl>
            <Input v-bind="componentField" :disabled="isPending" />
          </FormControl>
          <FormMessage />
        </FormItem>
      </FormField>
      <FormField v-slot="{ componentField }" name="district">
        <FormItem>
          <FormLabel>{{ t('common.data.district') }}</FormLabel>
          <FormControl>
            <Input v-bind="componentField" :disabled="isPending" />
          </FormControl>
          <FormMessage />
        </FormItem>
      </FormField>
      <FormField v-slot="{ componentField }" name="street">
        <FormItem class="col-start-1 col-end-3">
          <FormLabel>{{ t('common.data.addressData.street') }}</FormLabel>
          <FormControl>
            <Input v-bind="componentField" :disabled="isPending" />
          </FormControl>
          <FormMessage />
        </FormItem>
      </FormField>
      <FormField v-slot="{ componentField }" name="houseNumber">
        <FormItem>
          <FormLabel required>{{ t('common.data.addressData.houseNumber') }}</FormLabel>
          <FormControl>
            <Input v-bind="componentField" :disabled="isPending" />
          </FormControl>
          <FormMessage />
        </FormItem>
      </FormField>
      <FormField v-slot="{ componentField }" name="flatNumber">
        <FormItem>
          <FormLabel>{{ t('common.data.addressData.flatNumber') }}</FormLabel>
          <FormControl>
            <Input v-bind="componentField" :disabled="isPending" />
          </FormControl>
          <FormMessage />
        </FormItem>
      </FormField>
    </div>

    <ErrorBanner v-if="error" :error="error" />

    <slot />
  </form>
</template>
