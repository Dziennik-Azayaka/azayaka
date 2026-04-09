<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Calendar } from '@/components/ui/calendar';
import { FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { cn } from '@/lib/utils';
import { type CalendarDate, getLocalTimeZone } from '@internationalized/date';
import { LucideCalendar } from 'lucide-vue-next';
import type { FieldEntry } from 'vee-validate';
import { useI18n } from 'vue-i18n';

defineProps<{
  field: FieldEntry<unknown>;
  index: number;
  min: CalendarDate | undefined;
  max: CalendarDate | undefined;
  disabled: boolean;
}>();

const { locale, t, d } = useI18n();
</script>

<template>
  <FormField
    :key="field.key"
    v-slot="{ componentField }"
    :name="`periods[${index}].start`"
    class="contents"
  >
    <FormItem class="contents">
      <FormLabel>
        {{ t('administrator.classificationPeriods.startNPeriod', { number: index + 1 }) }}
      </FormLabel>
      <FormControl>
        <div
          class="aria-invalid:*:ring-destructive/20! dark:*:!aria-invalid:ring-destructive/40 aria-invalid:*:border-destructive!"
        >
          <Popover>
            <PopoverTrigger as-child>
              <Button
                variant="outline"
                :class="
                  cn(
                    'justify-start text-left font-normal w-full',
                    !componentField.modelValue && 'text-muted-foreground',
                  )
                "
                :disabled="disabled"
              >
                <LucideCalendar />
                {{
                  (componentField.modelValue as CalendarDate | undefined)
                    ? d(componentField.modelValue.toDate(getLocalTimeZone()))
                    : t('common.actions.chooseDate')
                }}
              </Button>
            </PopoverTrigger>
            <PopoverContent class="p-0 w-auto" align="start">
              <!-- @vue-ignore -->
              <Calendar
                layout="month-and-year"
                v-bind="componentField"
                :locale="locale"
                :min-value="min"
                :max-value="max"
                :default-placeholder="componentField.modelValue ?? min"
              />
            </PopoverContent>
          </Popover>
        </div>
      </FormControl>
      <FormMessage class="md:col-start-2 md:col-end-3" />
    </FormItem>
  </FormField>
</template>
