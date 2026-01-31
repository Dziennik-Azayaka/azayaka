<script setup lang="ts">
import { ErrorBanner } from "#ui/components/ui/banner";
import { useMutation, useQueryClient } from "@tanstack/vue-query";
import { LucideLock, LucidePrinter, LucideRefreshCw, LucideRotateCw } from "lucide-vue-next";
import { ref } from "vue";
import { useI18n } from "vue-i18n";

import {
    Button,
    Dialog,
    DialogClose,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from "@azayaka-frontend/ui";

import { AccessStatus as AccessStatusEnum } from "@/api/entities/access.ts";
import type { EmployeeAccessEntity } from "@/api/entities/employee-access.ts";
import AccessService from "@/api/services/access";
import AccessCode from "@/components/system-access/AccessCode.vue";
import AccessPrintableInstructions from "@/components/system-access/AccessPrintableInstructions.vue";
import AccessStatus from "@/components/system-access/AccessStatus.vue";

const props = defineProps<{ data: EmployeeAccessEntity; userRole: "employee" }>();

const queryClient = useQueryClient();
const { t, d } = useI18n();

const showDialog = ref(false);
const loading = ref<"regenerate" | "generate" | "revoke" | null>(null);
const instructionPrint = ref(false);

function getInstructionData() {
    if (props.data.status !== AccessStatusEnum.CODE_GENERATED) throw new Error("Activation code not available!");
    return {
        role: "employee" as const,
        header: `${props.data.fullName} (${props.data.shortcut})`,
        code: props.data.activationCode,
    };
}

const { isError, mutate: update } = useMutation({
    mutationKey: ["addSubject"],
    mutationFn: async (action: "regenerate" | "generate" | "revoke") => {
        loading.value = action;
        await AccessService.updateEmployeeAccesses([props.data.id], action);
    },
    onSuccess: async () => {
        await queryClient.invalidateQueries({ queryKey: ["accesses", props.userRole] });
        loading.value = null;
    },
});
</script>

<template>
    <Dialog v-model:open="showDialog">
        <DialogTrigger as-child>
            <slot />
        </DialogTrigger>
        <DialogContent class="!max-w-[40rem]">
            <DialogHeader>
                <DialogTitle>
                    <template v-if="userRole === 'employee'">
                        {{ data.fullName }} ({{ data.shortcut }}) - {{ t("employee") }}
                    </template>
                </DialogTitle>
            </DialogHeader>

            <AccessStatus :status="data.status" />
            <dl v-if="data.status === AccessStatusEnum.ACTIVE" class="space-y-1 mt-2">
                <div class="space-y-0.5">
                    <dt class="font-medium">{{ t("userActivationDate") }}:</dt>
                    <dd class="inline">{{ d(data.activatedAt, "long") }}</dd>
                </div>
                <div class="space-y-0.5">
                    <dt class="font-medium">{{ t("lastLoginDate") }}</dt>
                    <dd>{{ d(data.lastLoginAt, "long") }}</dd>
                </div>
            </dl>
            <AccessCode :code="data.activationCode" v-if="data.status === AccessStatusEnum.CODE_GENERATED" />

            <p
                class="rounded-md px-4 py-3.5 bg-primary text-primary-foreground text-sm"
                v-if="data.status === AccessStatusEnum.CODE_GENERATED"
            >
                {{ t("accessCodeDescription") }}
            </p>

            <p
                class="rounded-md px-4 py-3.5 bg-primary text-primary-foreground text-sm"
                v-if="data.status === AccessStatusEnum.INACTIVE"
            >
                {{ t("blockedAccessInfo") }}
            </p>

            <AccessPrintableInstructions
                :accesses="[getInstructionData()]"
                v-if="data.status === AccessStatusEnum.CODE_GENERATED && instructionPrint"
                @done="instructionPrint = false"
            />

            <p class="rounded-md px-4 py-3.5 bg-destructive text-primary-foreground text-sm" v-if="instructionPrint">
                {{ t("printAlert") }}
            </p>

            <ErrorBanner :description="t('unknownError')" v-if="isError" />

            <DialogFooter>
                <Button
                    variant="outline"
                    type="button"
                    @click="instructionPrint = true"
                    v-if="data.status === AccessStatusEnum.CODE_GENERATED"
                >
                    <LucidePrinter />
                    {{ t("printInstructions") }}
                </Button>
                <Button
                    variant="default"
                    type="button"
                    v-if="data.status === AccessStatusEnum.INACTIVE"
                    @click="update('generate')"
                    :disabled="loading"
                >
                    <LucideRefreshCw />
                    {{ t("generateCode") }}
                </Button>
                <Button
                    variant="outline"
                    type="button"
                    v-if="data.status === AccessStatusEnum.ACTIVE"
                    @click="update('regenerate')"
                    :disabled="loading"
                >
                    <LucideRotateCw />
                    {{ t("resetAccess") }}
                </Button>
                <Button
                    variant="destructive"
                    type="button"
                    v-if="data.status !== AccessStatusEnum.INACTIVE"
                    @click="update('revoke')"
                    :disabled="loading"
                >
                    <LucideLock />
                    {{ t("blockAccess") }}
                </Button>
                <div class="flex-1"></div>
                <DialogClose as-child>
                    <Button variant="outline" type="button" @click="instructionPrint = false">{{ t("close") }}</Button>
                </DialogClose>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
