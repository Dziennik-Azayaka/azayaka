<script setup lang="ts">
import { ErrorBanner } from '@/components/ui/banner';
import { classAddFormSchema, type ClassAddValues } from '../../forms/class-add';
import ClassFormTutorSelect from './ClassFormTutorSelect.vue';
import ClassPeriodSelector from './ClassPeriodSelector.vue';
import { useGetSchoolUnits } from '@/api/hooks/school-structure/getSchoolUnits';
import { Button } from '@/components/ui/button';
import { FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import {
  Select,
  SelectTrigger,
  SelectValue,
  SelectItem,
  SelectContent,
} from '@/components/ui/select';
import { Separator } from '@/components/ui/separator';
import { LucideRefreshCcw } from 'lucide-vue-next';
import { useForm } from 'vee-validate';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';

const emit = defineEmits<{ submit: [ClassAddValues] }>();
const props = defineProps<{ isPending: boolean, error: Error | null }>();

const { t } = useI18n();

const form = useForm({
  validationSchema: classAddFormSchema,
});

const { data: schoolUnits, isFetching, isError, refetch } = useGetSchoolUnits();
const activeUnits = computed(() => schoolUnits.value?.filter((unit) => unit.active));

const onSubmit = form.handleSubmit((values) => emit('submit', values));
</script>

<template>
  <form @submit="onSubmit" class="space-y-3">
    <div class="grid md:grid-cols-2 gap-3">
      <FormField v-slot="{ componentField }" name="schoolUnitId">
        <FormItem class="col-start-1 col-end-3">
          <FormLabel required>{{ t('common.data.schoolUnit') }}</FormLabel>
          <FormControl>
            <Select v-bind="componentField" :disabled="isPending">
              <SelectTrigger class="w-full">
                <SelectValue :placeholder="t('common.actions.select')" />
                <SelectContent>
                  <div class="p-3 flex flex-col gap-2.5 text-center text-sm" v-if="isError">
                    <span class="text-muted-foreground">{{ t('common.loadingDataError') }}</span>
                    <Button variant="outline" size="sm" @click="refetch">
                      <LucideRefreshCcw />
                      {{ t('common.actions.tryAgain') }}
                    </Button>
                  </div>
                  <div class="p-3 text-center text-sm text-muted-foreground" v-else-if="isFetching">
                    {{ t('common.pleaseWait') }}
                  </div>
                  <template v-else-if="activeUnits">
                    <SelectItem v-for="unit in activeUnits" :key="unit.id" :value="unit.id">
                      {{ unit.name }} ({{ unit.shortName }})
                    </SelectItem>
                  </template>
                </SelectContent>
              </SelectTrigger>
            </Select>
          </FormControl>
          <FormMessage />
        </FormItem>
      </FormField>
      <FormField v-slot="{ componentField }" name="mark">
        <FormItem>
          <FormLabel required>{{ t('administrator.classes.data.mark') }}</FormLabel>
          <FormControl>
            <Input v-bind="componentField" :disabled="isPending" />
          </FormControl>
          <FormMessage />
        </FormItem>
      </FormField>
      <FormField v-slot="{ componentField }" name="alias">
        <FormItem>
          <FormLabel>{{ t('administrator.classes.data.alias') }}</FormLabel>
          <FormControl>
            <Input v-bind="componentField" :disabled="isPending" />
          </FormControl>
          <FormMessage />
        </FormItem>
      </FormField>
      <FormField v-slot="{ componentField }" name="formTutors">
        <FormItem class="col-start-1 col-end-3">
          <FormLabel required>{{ t('administrator.classes.data.formTutors') }}</FormLabel>
          <FormControl>
            <ClassFormTutorSelect v-bind="componentField" :disabled="isPending" />
          </FormControl>
          <FormMessage />
        </FormItem>
      </FormField>
    </div>

    <Separator />

    <div class="grid md:grid-cols-2 gap-3">
      <FormField v-slot="{ componentField }" name="promoteEvery">
        <FormItem>
          <FormLabel required>{{ t('administrator.classes.data.promoteEvery.label') }}</FormLabel>
          <FormControl>
            <Select v-bind="componentField" :disabled="isPending">
              <SelectTrigger class="w-full">
                <SelectValue :placeholder="t('common.actions.select')" />
                <SelectContent>
                  <SelectItem value="year">
                    {{ t('administrator.classes.data.promoteEvery.year') }}
                  </SelectItem>
                  <SelectItem value="semester">
                    {{ t('administrator.classes.data.promoteEvery.semester') }}
                  </SelectItem>
                </SelectContent>
              </SelectTrigger>
            </Select>
          </FormControl>
          <FormMessage />
        </FormItem>
      </FormField>
      <FormField v-slot="{ componentField }" name="teachingCycleLength">
        <FormItem>
          <FormLabel required>{{ t('administrator.classes.data.teachingCycleLength') }}</FormLabel>
          <FormControl>
            <Input v-bind="componentField" type="number" :disabled="isPending" />
          </FormControl>
          <FormMessage />
        </FormItem>
      </FormField>
      <FormField
        v-slot="{ componentField }"
        name="startingClassificationPeriodId"
        v-if="form.values.schoolUnitId"
      >
        <FormItem>
          <FormLabel required>
            {{ t('administrator.classes.data.startingClassificationPeriod') }}
          </FormLabel>
          <FormControl>
            <ClassPeriodSelector :unit-id="form.values.schoolUnitId" v-bind="componentField" />
          </FormControl>
          <FormMessage />
        </FormItem>
      </FormField>
    </div>

    <ErrorBanner v-if="error" :error="error" />

    <slot />
  </form>
</template>
