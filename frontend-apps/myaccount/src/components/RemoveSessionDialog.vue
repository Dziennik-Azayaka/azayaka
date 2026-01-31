<script setup lang="ts">
import ErrorBanner from "#ui/components/ui/banner/ErrorBanner.vue";
import PasswordInput from "#ui/components/ui/input/PasswordInput.vue";
import { useMutation, useQueryClient } from "@tanstack/vue-query";
import { toTypedSchema } from "@vee-validate/zod";
import { LucideLoader2 } from "lucide-vue-next";
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
} from "@azayaka-frontend/ui";

import { ApiError } from "@/api/error";
import SessionApiService from "@/api/services/session";

const { t } = useI18n();
const showDialog = ref(false);

const props = defineProps<{ sessionId: string }>();

const formSchema = toTypedSchema(
    z.object({
        password: z.string({
            required_error: t("requiredPasswordError"),
        }),
    }),
);
const form = useForm({
    validationSchema: formSchema,
});

const error = ref<string | null>(null);
const queryClient = useQueryClient();

const { isPending, mutate } = useMutation({
    mutationKey: ["removeSession"],
    mutationFn: (password: string) => SessionApiService.removeSessionById(props.sessionId, password),
    onSuccess: async () => {
        showDialog.value = false;
        await queryClient.invalidateQueries({ queryKey: ["sessions"] });
    },
    onError: (reason) => {
        error.value = reason instanceof ApiError ? reason.getTranslationId() : "unknownError";
    },
});

const onSubmit = form.handleSubmit((values) => mutate(values.password));
</script>

<template>
    <Dialog v-model:open="showDialog">
        <DialogTrigger as-child>
            <Button size="sm" variant="secondary">{{ t("logOutSession") }}</Button>
        </DialogTrigger>
        <DialogContent>
            <form @submit="onSubmit" class="space-y-3">
                <DialogHeader>
                    <DialogTitle>{{ t("logOutSession") }}</DialogTitle>
                    <DialogDescription>{{ t("logOutSessionDescription") }}</DialogDescription>
                </DialogHeader>
                <FormField v-slot="{ componentField }" name="password">
                    <FormItem>
                        <FormLabel>{{ t("password") }}</FormLabel>
                        <FormControl>
                            <PasswordInput v-bind="componentField" :disabled="isPending" autocapitalize="off" />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>
                <ErrorBanner v-if="error" :description="t(error)" />
                <DialogFooter>
                    <DialogClose as-child>
                        <Button variant="outline" type="button">{{ t("cancel") }}</Button>
                    </DialogClose>
                    <Button type="submit">
                        <template v-if="!isPending">{{ t("confirm") }}</template>
                        <LucideLoader2 v-else class="animate-spin size-5" :aria-label="t('pleaseWait')" />
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>
