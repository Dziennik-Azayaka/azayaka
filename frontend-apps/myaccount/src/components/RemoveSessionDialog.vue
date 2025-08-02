<script setup lang="ts">
import PasswordInput from "#ui/components/ui/input/PasswordInput.vue";
import ErrorBanner from "./ErrorBanner.vue";
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

import { IncorrectPasswordError } from "@/api/errors";
import SessionApiService from "@/api/services/session";
import { useMainStore } from "@/stores/main.store";

const { t } = useI18n();
const mainStore = useMainStore();
const showDialog = ref(false);

const props = defineProps<{ sessionId: string }>();
const emit = defineEmits(["logout"]);

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

const isLoading = ref(false);
const error = ref<string | null>();

const onSubmit = form.handleSubmit(async (values) => {
    isLoading.value = true;
    error.value = null;
    try {
        await SessionApiService.removeSessionById(props.sessionId, values.password);
        emit("logout");
        showDialog.value = false;
    } catch (reason) {
        if (reason instanceof IncorrectPasswordError) error.value = "incorrectPasswordError";
        else error.value = "unknownError";
    } finally {
        isLoading.value = false;
    }
});
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
                            <PasswordInput v-bind="componentField" :disabled="isLoading" autocapitalize="off" />
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
                        <template v-if="!isLoading">{{ t("confirm") }}</template>
                        <LucideLoader2 v-else class="animate-spin size-5" :aria-label="t('pleaseWait')" />
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>
