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
    Input,
} from "@azayaka-frontend/ui";

const { t } = useI18n();

const formSchema = toTypedSchema(
    z.object({
        emailAddress: z
            .string({
                required_error: t("requiredEmailAddressError"),
            })
            .email(t("invalidEmailAddressError")),
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
                    <DialogTitle>Zmień adres e-mail</DialogTitle>
                    <DialogDescription
                        >Ustaw nowy adres e-mail, który będziesz używać do logowania się do dziennika
                        Azayaka.</DialogDescription
                    >
                </DialogHeader>
                <FormField v-slot="{ componentField }" name="emailAddress">
                    <FormItem>
                        <FormLabel>{{ t("newEmailAddress") }}</FormLabel>
                        <FormControl>
                            <Input type="text" v-bind="componentField" :disabled="isLoading" autocapitalize="off" />
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
