<script setup lang="ts">
import ClassSelectPeriod from "./ClassSelectPeriod.vue";
import { toTypedSchema } from "@vee-validate/zod";
import { useForm } from "vee-validate";
import { watch } from "vue";
import { useI18n } from "vue-i18n";
import z from "zod";

import {
    FormControl,
    FormField,
    FormItem,
    FormLabel,
    FormMessage,
    Input,
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
    Separator,
    optionalStringField,
    requiredStringField,
} from "@azayaka-frontend/ui";

import type { SchoolUnitEntity } from "@/api/entities/school-structure";
import type { ClassGeneralInfoForm } from "@/types";

const emit = defineEmits(["submit"]);
const props = defineProps<{ schoolUnits: SchoolUnitEntity[]; loading: boolean; currentData?: ClassGeneralInfoForm }>();

const { t } = useI18n();

const formSchema = toTypedSchema(
    z.object({
        schoolUnitId: z.number({ required_error: t("requiredFieldError") }),
        mark: requiredStringField(t, { maxLength: 3 }),
        alias: optionalStringField(t, { maxLength: 64 }),
        promoteEvery: z.enum(["year", "semester"], { required_error: t("requiredFieldError") }),
        teachingCycleLength: z
            .number({ required_error: t("requiredFieldError") })
            .min(2)
            .max(8),
        startingClassificationPeriod: z.object(
            {
                id: z.number(),
                number: z.number(),
                schoolYear: z.number(),
            },
            { required_error: t("requiredFieldError") },
        ),
    }),
);

const form = useForm({
    validationSchema: formSchema,
    initialValues: props.currentData,
});

const onSubmit = form.handleSubmit((values) => {
    emit("submit", values);
});

watch(
    () => form.values.schoolUnitId,
    (unitId) => {
        if (unitId === undefined || form.values.startingClassificationPeriod === undefined) return;
        form.setFieldValue("startingClassificationPeriod", undefined, false);
    },
);
</script>

<template>
    <form @submit="onSubmit">
        <div class="grid sm:grid-cols-2 gap-3 mb-3">
            <FormField v-slot="{ componentField }" name="schoolUnitId">
                <FormItem class="sm:col-start-1 sm:col-end-3 h-min">
                    <FormLabel>{{ t("schoolUnit") }}<span class="text-destructive">*</span></FormLabel>
                    <FormControl>
                        <Select v-bind="componentField">
                            <SelectTrigger class="w-full">
                                <SelectValue :placeholder="t('select')" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem v-for="unit in schoolUnits" :value="unit.id" :key="unit.id">
                                    {{ unit.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                    </FormControl>
                    <FormMessage />
                </FormItem>
            </FormField>
            <FormField v-slot="{ componentField }" name="mark">
                <FormItem>
                    <FormLabel>{{ t("mark") }}<span class="text-destructive">*</span></FormLabel>
                    <FormControl>
                        <Input v-bind="componentField" />
                    </FormControl>
                    <FormMessage />
                </FormItem>
            </FormField>
            <FormField v-slot="{ componentField }" name="alias">
                <FormItem>
                    <FormLabel>{{ t("alias") }}</FormLabel>
                    <FormControl>
                        <Input v-bind="componentField" />
                    </FormControl>
                    <FormMessage />
                </FormItem>
            </FormField>
            <Separator class="sm:col-start-1 sm:col-end-3" />
            <FormField v-slot="{ componentField }" name="promoteEvery">
                <FormItem>
                    <FormLabel>{{ t("promoteEvery") }}<span class="text-destructive">*</span></FormLabel>
                    <FormControl>
                        <Select v-bind="componentField">
                            <SelectTrigger class="w-full">
                                <SelectValue :placeholder="t('select')" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="year">{{ t("promoteEveryYear") }}</SelectItem>
                                <SelectItem value="semester">{{ t("promoteEveryPeriod") }}</SelectItem>
                            </SelectContent>
                        </Select>
                    </FormControl>
                    <FormMessage />
                </FormItem>
            </FormField>
            <FormField v-slot="{ componentField }" name="teachingCycleLength">
                <FormItem>
                    <FormLabel>{{ t("teachingCycleLength") }}<span class="text-destructive">*</span></FormLabel>
                    <FormControl>
                        <Input type="number" v-bind="componentField" min="2" max="8" />
                    </FormControl>
                    <FormMessage />
                </FormItem>
            </FormField>
            <FormField v-slot="{ componentField }" name="startingClassificationPeriod">
                <FormItem>
                    <FormLabel>{{ t("firstClassificationPeriod") }}<span class="text-destructive">*</span></FormLabel>
                    <FormControl>
                        <ClassSelectPeriod v-bind="componentField" :unit-id="form.values.schoolUnitId" />
                    </FormControl>
                    <FormMessage />
                </FormItem>
            </FormField>
        </div>
        <slot name="footer" />
    </form>
</template>
