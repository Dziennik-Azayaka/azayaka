<script setup lang="ts">
import PasswordInput from "#ui/components/ui/input/PasswordInput.vue";
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
import UserApiService from "@/api/services/user";
import { AlreadyTakenEmailAddressError, IncorrectPasswordError } from "@/api/errors";
import { useMainStore } from "@/stores/main.store";
import ErrorBanner from "./ErrorBanner.vue";

const { t } = useI18n();
const mainStore = useMainStore();
const showDialog = ref(false);

const formSchema = toTypedSchema(
    z.object({
        emailAddress: z
            .string({
                required_error: t("requiredEmailAddressError"),
            })
            .email(t("invalidEmailAddressError")),
		password: z
			.string({
                required_error: t("requiredPasswordError"),
            })
    }),
);
const form = useForm({
    validationSchema: formSchema,
});

const isLoading = ref(false);
const error = ref<string | null>();

const onSubmit = form.handleSubmit(async (values) => {
    isLoading.value = true;
	error.value = null;
	try {
		await UserApiService.setNewEmailAddress(values.emailAddress, values.password);
		mainStore.emailAddress = values.emailAddress;
		showDialog.value = false;

	} catch (reason) {
		if (reason instanceof IncorrectPasswordError) error.value = "incorrectPasswordError";
		else if (reason instanceof AlreadyTakenEmailAddressError) error.value = "alreadyTakenEmailAddressError"
		else error.value = "unknownError";
	} finally {
		isLoading.value = false;
	}
});

</script>

<template>
    <Dialog v-model:open="showDialog">
        <DialogTrigger as-child>
            <Button size="sm" variant="secondary">{{ t("change") }}</Button>
        </DialogTrigger>
        <DialogContent>
            <form @submit="onSubmit" class="space-y-3">
                <DialogHeader>
                    <DialogTitle>{{ t("changeEmailAddress") }}</DialogTitle>
                    <DialogDescription>{{ t("changeEmailAddressDescription") }}</DialogDescription>
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
                <FormField v-slot="{ componentField }" name="password">
                    <FormItem>
                        <FormLabel>{{ t("currentPassword") }}</FormLabel>
                        <FormControl>
                            <PasswordInput v-bind="componentField" :disabled="isLoading" autocapitalize="off" />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>
				<ErrorBanner v-if="error" :description="error" />
                <DialogFooter>
                    <DialogClose as-child>
                        <Button variant="outline" type="button">{{ t("cancel") }}</Button>
                    </DialogClose>
                    <Button type="submit">{{ t("confirm") }}</Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>
