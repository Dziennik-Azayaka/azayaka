<script setup lang="ts">
import { ref, watch } from "vue";
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
import AccessCode from "@/components/system-access/AccessCode.vue";
import AccessStatus from "@/components/system-access/AccessStatus.vue";

defineProps<{ data: EmployeeAccessEntity; userRole: "employee" }>();
const { t, d } = useI18n();
const showDialog = ref(false);
const error = ref<string | null>(null);

watch(showDialog, (value) => {
    if (value) error.value = null;
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

            <DialogFooter>
                <DialogClose as-child>
                    <Button variant="outline" type="button">{{ t("close") }}</Button>
                </DialogClose>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
