<script setup lang="ts">
import { Button } from "#ui/components/ui/button";
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogTitle,
} from "#ui/components/ui/dialog";
import { LucidePrinter } from "lucide-vue-next";
import { computed, ref } from "vue";
import { useI18n } from "vue-i18n";

import { AccessStatus } from "@/api/entities/access.ts";
import type { EmployeeAccessEntity } from "@/api/entities/employee-access.ts";
import AccessPrintableInstructions from "@/components/system-access/AccessPrintableInstructions.vue";

const props = defineProps<{ accessesType: "employee"; selectedAccesses: EmployeeAccessEntity[] }>();
const { t } = useI18n();
const print = ref(false);
const showWarningDialog = ref(false);

const preparedAccesses = computed(() =>
    props.selectedAccesses
        .map((access) => {
            if (access.status !== AccessStatus.CODE_GENERATED) return;
            if (props.accessesType === "employee")
                return {
                    role: "employee" as const,
                    header: `${access.fullName} (${access.shortcut})`,
                    code: access.activationCode,
                };
        })
        .filter((access) => !!access),
);

function onPrintClick() {
    if (props.selectedAccesses.find((access) => access.status !== AccessStatus.CODE_GENERATED))
        showWarningDialog.value = true;
    else print.value = true;
}

const disabled = computed(
    () => !props.selectedAccesses.find((access) => access.status === AccessStatus.CODE_GENERATED),
);
</script>

<template>
    <Button @click="onPrintClick()" :disabled="disabled" class="not-lg:w-full">
        <LucidePrinter />
        {{ t("printInstructionsForSelected") }}
    </Button>

    <AccessPrintableInstructions :accesses="preparedAccesses" @done="print = false" v-if="print" />

    <Dialog v-model:open="showWarningDialog">
        <DialogContent>
            <DialogTitle>{{ t("warning") }}</DialogTitle>
            <DialogDescription>
                {{ t("accessInstructionsWarning") }}
            </DialogDescription>
            <DialogFooter>
                <DialogClose as-child>
                    <Button variant="outline">{{ t("close") }}</Button>
                </DialogClose>
                <DialogClose @click="print = true" as-child>
                    <Button>{{ t("continue") }}</Button>
                </DialogClose>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
