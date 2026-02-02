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
    requiredStringField,
} from "@azayaka-frontend/ui";

import type { SubjectEntity } from "@/api/entities/subject.ts";

const { t } = useI18n();
const props = defineProps<{
    currentData?: SubjectEntity;
    errorMessage: string | null;
    loading: boolean;
}>();

const emit = defineEmits(["submit"]);

const formSchema = toTypedSchema(
    z.object({
        name: requiredStringField(t, { minLength: 3 }),
        shortcut: requiredStringField(t, { maxLength: 32 }),
    }),
);
const form = useForm({
    validationSchema: formSchema,
    initialValues: props.currentData,
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

        <FormField v-slot="{ componentField }" name="shortcut">
            <FormItem>
                <FormLabel>{{ t("short") }}<span class="text-destructive">*</span></FormLabel>
                <FormControl>
                    <Input v-bind="componentField" :disabled="loading" />
                </FormControl>
                <FormMessage />
            </FormItem>
        </FormField>

        <ErrorBanner v-if="errorMessage" :description="t(errorMessage)" />

        <slot name="footer" />
    </form>
</template>
