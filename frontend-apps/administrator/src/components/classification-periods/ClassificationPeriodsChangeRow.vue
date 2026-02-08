<script setup lang="ts">
import { cn } from "#ui/lib/utils";
import { type CalendarDate, getLocalTimeZone } from "@internationalized/date";
import { LucideCalendar } from "lucide-vue-next";
import type { FieldEntry } from "vee-validate";
import { useI18n } from "vue-i18n";

import {
    Button,
    Calendar,
    FormControl,
    FormField,
    FormItem,
    FormLabel,
    FormMessage,
    Popover,
    PopoverContent,
    PopoverTrigger,
} from "@azayaka-frontend/ui";

defineProps<{
    field: FieldEntry<unknown>;
    index: number;
    min: CalendarDate | undefined;
    max: CalendarDate | undefined;
}>();

const { locale, t, d } = useI18n();
</script>

<template>
    <FormField :key="field.key" v-slot="{ componentField }" :name="`periods[${index}].start`" class="contents">
        <FormItem class="contents">
            <FormLabel>
                {{ t("startNPeriod", { number: index + 1 }) }}
            </FormLabel>
            <FormControl>
                <div
                    class="aria-invalid:*:!ring-destructive/20 dark:*:!aria-invalid:ring-destructive/40 aria-invalid:*:!border-destructive"
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
                                :disabled="!index"
                            >
                                <LucideCalendar />
                                {{
                                    (componentField.modelValue as CalendarDate | undefined)
                                        ? d(componentField.modelValue.toDate(getLocalTimeZone()))
                                        : t("chooseDate")
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
