<script setup lang="ts">
import { DialogTrigger } from "#ui/components/ui/dialog";
import { LucideLoader2 } from "lucide-vue-next";
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
const loading = ref(false);
const error = ref<string | null>(null);

async function onSubmit(values: SubjectForm) {
    loading.value = true;
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
        loading.value = false;
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
            <SubjectDialogForm @submit="onSubmit" :error-message="error" :loading="loading" :current-data="currentData">
                <template #footer>
                    <DialogFooter>
                        <DialogClose as-child>
                            <Button variant="outline" type="button">{{ t("cancel") }}</Button>
                        </DialogClose>
                        <Button type="submit">
                            <template v-if="!loading">{{ t("save") }}</template>
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
