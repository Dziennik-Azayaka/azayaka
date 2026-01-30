<script setup lang="ts">
import { DialogTrigger } from "#ui/components/ui/dialog";
import { useMutation, useQueryClient } from "@tanstack/vue-query";
import { LucideArchive, LucideLoader2 } from "lucide-vue-next";
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

import type { SubjectEntity } from "@/api/entities/subject.ts";
import { ApiError } from "@/api/error";
import SubjectService from "@/api/services/subject";
import SubjectDialogForm from "@/components/subjects/SubjectDialogForm.vue";
import type { SubjectForm } from "@/types";

const props = defineProps<{ currentData: SubjectEntity }>();

const { t } = useI18n();
const queryClient = useQueryClient();

const showDialog = ref(false);
const loading = ref<"edit" | "change-activity" | null>(null);
const error = ref<string | null>(null);

const { mutate: onSubmit } = useMutation({
    mutationKey: ["editSubject"],
    mutationFn: async (form: SubjectForm) => {
        loading.value = "edit";
        await SubjectService.editSubject(props.currentData.id, form);
    },
    onSuccess: async () => {
        showDialog.value = false;
        await queryClient.invalidateQueries({ queryKey: ["subjects"] });
        loading.value = null;
    },
    onError: (reason) => {
        error.value = reason instanceof ApiError ? reason.getTranslationId() : "unknownError";
        loading.value = null;
    },
});

const { mutate: changeActivity } = useMutation({
    mutationKey: ["archiveSubject"],
    mutationFn: async () => {
        loading.value = "change-activity";
        await SubjectService.changeSubjectActivity(props.currentData.id);
    },
    onSuccess: async () => {
        showDialog.value = false;
        await queryClient.invalidateQueries({ queryKey: ["subjects"] });
        loading.value = null;
    },
    onError: (reason) => {
        error.value = reason instanceof ApiError ? reason.getTranslationId() : "unknownError";
        loading.value = null;
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
                <DialogTitle>{{ t("editSubject") }}</DialogTitle>
                <DialogDescription>{{ t("requiredFieldsInfo") }}</DialogDescription>
            </DialogHeader>
            <SubjectDialogForm
                @submit="onSubmit"
                :error-message="error"
                :loading="!!loading"
                :current-data="currentData"
            >
                <template #footer>
                    <DialogFooter>
                        <Button
                            type="button"
                            :variant="currentData.active ? 'destructive' : 'outline'"
                            @click="changeActivity"
                            :disabled="loading"
                        >
                            <template v-if="loading !== 'change-activity'">
                                <LucideArchive aria-hidden="true" />
                                <template v-if="currentData.active">{{ t("moveToArchive") }}</template>
                                <template v-else>{{ t("unarchive") }}</template>
                            </template>
                            <LucideLoader2 v-else class="animate-spin size-5" :aria-label="t('pleaseWait')" />
                        </Button>
                        <div class="flex-1"></div>
                        <DialogClose as-child>
                            <Button variant="outline" type="button">{{ t("cancel") }}</Button>
                        </DialogClose>
                        <Button type="submit" :disabled="loading">
                            <template v-if="loading !== 'edit'">{{ t("save") }}</template>
                            <LucideLoader2 v-else class="animate-spin size-5" :aria-label="t('pleaseWait')" />
                        </Button>
                    </DialogFooter>
                </template>
            </SubjectDialogForm>
        </DialogContent>
        <DialogTrigger as-child>
            <slot />
        </DialogTrigger>
    </Dialog>
</template>
