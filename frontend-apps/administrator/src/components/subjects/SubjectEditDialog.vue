<script setup lang="ts">
import { DialogTrigger } from "#ui/components/ui/dialog";
import { LucideArchive, LucideLoader2 } from "lucide-vue-next";
import { ref } from "vue";
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
import { TakenNameError, TakenShortcutError } from "@/api/errors.ts";
import SubjectService from "@/api/services/subject";
import SubjectDialogForm from "@/components/subjects/SubjectDialogForm.vue";
import type { SubjectForm } from "@/types";

const { t } = useI18n();
const emit = defineEmits(["edited"]);
const props = defineProps<{ currentData: SubjectEntity }>();

const showDialog = ref(false);
const loading = ref<"edit" | "change-activity" | null>(null);
const error = ref<string | null>(null);

async function onSubmit(values: SubjectForm) {
    loading.value = "edit";
    error.value = null;
    try {
        await SubjectService.editSubject(props.currentData.id, values);
        emit("edited");
        showDialog.value = false;
    } catch (reason) {
        if (reason instanceof TakenNameError) error.value = "takenNameError";
        else if (reason instanceof TakenShortcutError) error.value = "takenShortcutError";
        else error.value = "unknownError";
    } finally {
        loading.value = null;
    }
}

async function changeActivity() {
    loading.value = "change-activity";
    error.value = null;
    try {
        await SubjectService.changeSubjectActivity(props.currentData.id);
        emit("edited");
        showDialog.value = false;
    } catch {
        error.value = "unknownError";
    } finally {
        loading.value = null;
    }
}
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
                        <Button type="submit">
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
