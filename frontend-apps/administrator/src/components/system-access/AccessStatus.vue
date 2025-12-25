<script setup lang="ts">
import { LucideLogIn, LucideRectangleEllipsis, LucideUser, LucideX } from "lucide-vue-next";
import { useI18n } from "vue-i18n";

import { AccessStatus } from "@/api/entities/access.ts";

defineProps<{ status: AccessStatus }>();

const { t } = useI18n();
</script>

<template>
    <div class="flex">
        <div class="flex-1 flex flex-col gap-1 items-center relative">
            <div class="size-12 rounded-full bg-primary text-primary-foreground flex items-center justify-center">
                <LucideUser />
            </div>
            <div
                class="absolute left-[calc(50%+25px)] right-[calc(-50%+10px)] top-6 block h-0.5 shrink-0 rounded-full bg-muted group-data-[state=completed]:bg-primary"
            />
            <div class="flex flex-col items-center">
                <div class="text-sm font-medium">{{ t("userCreated") }}</div>
            </div>
        </div>
        <div class="flex-1 flex flex-col gap-1 items-center relative">
            <div
                class="size-12 rounded-full bg-destructive text-primary-foreground flex items-center justify-center"
                :class="{
                    'bg-destructive': status === AccessStatus.INACTIVE,
                    'bg-primary': status !== AccessStatus.INACTIVE,
                }"
            >
                <LucideX v-if="status === AccessStatus.INACTIVE" />
                <LucideRectangleEllipsis v-else />
            </div>
            <div
                class="absolute left-[calc(50%+25px)] right-[calc(-50%+25px)] top-6 block h-0.5 shrink-0 rounded-full bg-muted group-data-[state=completed]:bg-primary"
            />
            <div class="flex flex-col items-center">
                <div class="text-sm font-medium">
                    <template v-if="status === AccessStatus.INACTIVE">{{ t("accessBlocked") }}</template>
                    <template v-else>{{ t("codeGenerated") }}</template>
                </div>
            </div>
        </div>
        <div class="flex-1 flex flex-col gap-1 items-center">
            <div
                class="size-12 rounded-full flex items-center justify-center"
                :class="{
                    'bg-accent text-accent-foreground': status === AccessStatus.INACTIVE,
                    'bg-destructive text-primary-foreground': status === AccessStatus.CODE_GENERATED,
                    'bg-primary text-primary-foreground': status === AccessStatus.ACTIVE,
                }"
            >
                <LucideLogIn />
            </div>
            <div class="flex flex-col items-center">
                <div class="text-sm font-medium text-center">
                    <template v-if="status === AccessStatus.ACTIVE">{{ t("userActivatedAccess") }}</template>
                    <template v-else>{{ t("userHasntActivateAccess") }}</template>
                </div>
            </div>
        </div>
    </div>
</template>
