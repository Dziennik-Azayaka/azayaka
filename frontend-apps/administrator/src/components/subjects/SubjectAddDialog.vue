<script setup lang="ts">
import { DialogTrigger } from "#ui/components/ui/dialog";
import { useMutation, useQueryClient } from "@tanstack/vue-query";
import { LucideLoader2, LucidePlus } from "lucide-vue-next";
import { ref, watch } from "vue";
import { useI18n } from "vue-i18n";

import {
    Button,
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from "@azayaka-frontend/ui";

import { ApiError } from "@/api/error";
import SubjectService from "@/api/services/subject";
import SubjectDialogForm from "@/components/subjects/SubjectDialogForm.vue";
import type { SubjectForm } from "@/types";

const { t } = useI18n();
const queryClient = useQueryClient();

const showDialog = ref(false);
const error = ref<string | null>(null);

const { isPending, mutate: onSubmit } = useMutation({
    mutationKey: ["addSubject"],
    mutationFn: async (form: SubjectForm) => {
        await SubjectService.addSubject(form);
    },
    onSuccess: async () => {
        showDialog.value = false;
        await queryClient.invalidateQueries({ queryKey: ["subjects"] });
    },
    onError: (reason) => {
        error.value = reason instanceof ApiError ? reason.getTranslationId() : "unknownError";
    },
});

watch(showDialog, (value) => {
    if (value) error.value = null;
});
</script>

<template>
    <Dialog v-model:open="showDialog">
        <DialogContent class="!max-w-[40rem]">
            <DialogHeader>
                <DialogTitle>{{ t("addSubject") }}</DialogTitle>
                <DialogDescription>{{ t("requiredFieldsInfo") }}</DialogDescription>
            </DialogHeader>
            <SubjectDialogForm @submit="onSubmit" :error-message="error" :loading="isPending">
                <template #footer>
                    <DialogFooter>
                        <DialogClose as-child>
                            <Button variant="outline" type="button">{{ t("cancel") }}</Button>
                        </DialogClose>
                        <Button type="submit">
                            <template v-if="!isPending">{{ t("save") }}</template>
                            <LucideLoader2 v-else class="animate-spin size-5" :aria-label="t('pleaseWait')" />
                        </Button>
                    </DialogFooter>
                </template>
            </SubjectDialogForm>
        </DialogContent>
        <DialogTrigger as-child>
            <Button>
                {{ t("addSubject") }}
                <LucidePlus />
            </Button>
        </DialogTrigger>
    </Dialog>
</template>
