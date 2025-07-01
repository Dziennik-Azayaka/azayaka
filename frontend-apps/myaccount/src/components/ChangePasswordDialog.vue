<script setup lang="ts">
import { toTypedSchema } from "@vee-validate/zod";
import { useForm } from "vee-validate";
import { ref } from "vue";
import { useI18n } from "vue-i18n";
import z from "zod";

import {
    Button,
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
    FormControl,
    FormField,
    FormItem,
    FormLabel,
    FormMessage,
    PasswordInput,
} from "@azayaka-frontend/ui";

const { t } = useI18n();

const formSchema = toTypedSchema(
    z
        .object({
            currentPassword: z.string({
                required_error: t("requiredPasswordError"),
            }),
            newPassword: z
                .string({
                    required_error: t("requiredPasswordError"),
                })
                .min(8, t("passwordMinLengthError"))
                .max(128, t("passwordMaxLengthError")),
            repeatPassword: z.string({
                required_error: t("requiredPasswordError"),
            }),
        })
        .refine((values) => values.newPassword === values.repeatPassword, {
            message: t("repeatPasswordError"),
            path: ["repeatPassword"],
        }),
);
const form = useForm({
    validationSchema: formSchema,
});

const onSubmit = form.handleSubmit((values) => {
    console.log(values);
});
const isLoading = ref(false);
</script>

<template>
    <Dialog>
        <DialogTrigger as-child>
            <Button size="sm" variant="secondary">{{ t("change") }}</Button>
        </DialogTrigger>
        <DialogContent>
            <form @submit="onSubmit" class="space-y-3">
                <DialogHeader>
                    <DialogTitle>Zmień hasło</DialogTitle>
                    <DialogDescription>
                        Ustaw nowye hasło, który będziesz używać do logowania się do dziennika Azayaka.
                    </DialogDescription>
                </DialogHeader>
                <FormField v-slot="{ componentField }" name="currentPassword">
                    <FormItem>
                        <FormLabel>{{ t("currentPassword") }}</FormLabel>
                        <FormControl>
                            <PasswordInput v-bind="componentField" :disabled="isLoading" />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>
                <FormField v-slot="{ componentField }" name="newPassword">
                    <FormItem>
                        <FormLabel>{{ t("newPassword") }}</FormLabel>
                        <FormControl>
                            <PasswordInput v-bind="componentField" :disabled="isLoading" />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>
                <FormField v-slot="{ componentField }" name="repeatPassword">
                    <FormItem>
                        <FormLabel>{{ t("repeatPassword") }}</FormLabel>
                        <FormControl>
                            <PasswordInput v-bind="componentField" :disabled="isLoading" />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>
                <DialogFooter>
                    <DialogClose as-child>
                        <Button variant="outline" type="button">Anuluj</Button>
                    </DialogClose>
                    <Button type="submit">Potwierdź</Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>
