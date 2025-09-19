<script setup lang="ts">
import { toTypedSchema } from "@vee-validate/zod";
import { useForm } from "vee-validate";
import { useI18n } from "vue-i18n";
import z from "zod";

import {
    ErrorBanner,
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
} from "@azayaka-frontend/ui";

import type { SchoolUnitEntity } from "@/api/entities/school-structure";
import InstitutionTypes from "@/resources/institution-types.json";
import StudentCategories from "@/resources/student-categories.json";
import Voivodeships from "@/resources/voivodeships.json";

const { t } = useI18n();
const { currentData, errorMessage, loading } = defineProps<{
    currentData?: SchoolUnitEntity;
    errorMessage: string | null;
    loading: boolean;
}>();

const emit = defineEmits(["submit"]);

const formSchema = toTypedSchema(
    z.object({
        name: z
            .string({
                required_error: t("requiredFieldError"),
            })
            .min(1, {
                message: t("requiredFieldError"),
            })
            .max(255, t("fieldMaxLengthError", { number: 255 })),
        shortName: z
            .string({
                required_error: t("requiredFieldError"),
            })
            .min(1, {
                message: t("requiredFieldError"),
            })
            .max(255, t("fieldMaxLengthError", { number: 255 })),
        typeId: z.number({
            required_error: t("requiredFieldError"),
        }),
        address: z
            .string({
                required_error: t("requiredFieldError"),
            })
            .min(1, {
                message: t("requiredFieldError"),
            })
            .max(255, t("fieldMaxLengthError", { number: 255 })),
        voivodeshipId: z.number({ required_error: t("requiredFieldError") }),
        commune: z
            .string({ required_error: t("requiredFieldError") })
            .min(1, {
                message: t("requiredFieldError"),
            })
            .max(255, t("fieldMaxLengthError", { number: 255 })),
        district: z
            .string()
            .max(255, t("fieldMaxLengthError", { number: 255 }))
            .nullable()
            .optional(),
        studentCategory: z.string({ required_error: t("requiredFieldError") }),
    }),
);
const form = useForm({
    validationSchema: formSchema,
    initialValues: currentData,
});

const onSubmit = form.handleSubmit((values) => {
    emit("submit", values);
});
</script>

<template>
    <form @submit="onSubmit" class="space-y-3">
        <FormField v-slot="{ componentField }" name="name">
            <FormItem>
                <FormLabel>{{ t("name") }}<span class="text-destructive">*</span></FormLabel>
                <FormControl>
                    <Input v-bind="componentField" :disabled="loading" />
                </FormControl>
                <FormMessage />
            </FormItem>
        </FormField>

        <div class="grid sm:grid-cols-2 gap-3">
            <FormField v-slot="{ componentField }" name="shortName">
                <FormItem>
                    <FormLabel>{{ t("short") }}<span class="text-destructive">*</span></FormLabel>
                    <FormControl>
                        <Input v-bind="componentField" :disabled="loading" />
                    </FormControl>
                    <FormMessage />
                </FormItem>
            </FormField>
            <FormField v-slot="{ componentField }" name="typeId">
                <FormItem class="grid-cols-[1fr]">
                    <FormLabel>{{ t("institutionType") }}<span class="text-destructive">*</span></FormLabel>
                    <FormControl>
                        <Select v-bind="componentField" :disabled="loading" class="w-max">
                            <SelectTrigger class="w-full">
                                <SelectValue :placeholder="t('select')" class="overflow-hidden w-max text-ellipsis" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem v-for="{ nameTranslationId, id } in InstitutionTypes" :key="id" :value="id">
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
                    <FormLabel>{{ t("studentCategory") }}<span class="text-destructive">*</span></FormLabel>
                    <FormControl>
                        <Select v-bind="componentField" :disabled="loading">
                            <SelectTrigger class="w-full">
                                <SelectValue :placeholder="t('select')" />
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

        <div class="h-4" />

        <FormField v-slot="{ componentField }" name="address">
            <FormItem>
                <FormLabel>{{ t("address") }}<span class="text-destructive">*</span></FormLabel>
                <FormControl>
                    <Input v-bind="componentField" :disabled="loading" />
                </FormControl>
                <FormMessage />
            </FormItem>
        </FormField>

        <div class="grid sm:grid-cols-2 gap-3">
            <FormField v-slot="{ componentField }" name="voivodeshipId">
                <FormItem>
                    <FormLabel>{{ t("voivodeship") }}<span class="text-destructive">*</span></FormLabel>
                    <FormControl>
                        <Select v-bind="componentField" :disabled="loading">
                            <SelectTrigger class="w-full">
                                <SelectValue :placeholder="t('select')" />
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
            <FormField v-slot="{ componentField }" name="commune">
                <FormItem>
                    <FormLabel>{{ t("commune") }}<span class="text-destructive">*</span></FormLabel>
                    <FormControl>
                        <Input v-bind="componentField" :disabled="loading" />
                    </FormControl>
                    <FormMessage />
                </FormItem>
            </FormField>
            <FormField v-slot="{ componentField }" name="district">
                <FormItem>
                    <FormLabel>{{ t("district") }}</FormLabel>
                    <FormControl>
                        <Input v-bind="componentField" :disabled="loading" />
                    </FormControl>
                    <FormMessage />
                </FormItem>
            </FormField>
        </div>

        <ErrorBanner v-if="errorMessage" :description="t(errorMessage)" />

        <slot name="footer" />
    </form>
</template>
