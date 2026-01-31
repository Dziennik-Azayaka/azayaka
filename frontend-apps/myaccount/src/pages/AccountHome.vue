<script setup lang="ts">
import { Button } from "#ui/components/ui/button";
import { useQuery } from "@tanstack/vue-query";
import {
    LucideAlertCircle,
    LucideHelpCircle,
    LucideLaptop,
    LucideLoaderCircle,
    LucideSmartphone,
    RefreshCw,
} from "lucide-vue-next";
import { useI18n } from "vue-i18n";

import SessionApiService from "@/api/services/session.ts";
import PanelHeader from "@/components/PanelHeader.vue";
import RemoveSessionDialog from "@/components/RemoveSessionDialog.vue";

const { t, d } = useI18n();

const getDeviceTypeByOS = (os: string) => {
    if (os.startsWith("iOS") || os.startsWith("Android")) return "mobile";
    if (os.startsWith("Linux") || os.startsWith("Windows") || os.startsWith("macOS")) return "desktop";
    return "other";
};

const {
    data: sessions,
    isError,
    isLoading,
    refetch,
} = useQuery({ queryKey: ["sessions"], queryFn: SessionApiService.getActiveSessions });
</script>

<template>
    <div>
        <PanelHeader :breadcrumb-path="[{ href: { name: 'home' }, title: t('home') }]" />
        <main id="main-content">
            <h2 class="text-2xl font-semibold">{{ t("home") }}</h2>
            <p class="text-foreground/70 mb-4 text-sm">{{ t("homeDescription") }}</p>
            <section>
                <h3 class="font-semibold my-3">{{ t("activeSessions") }}</h3>
                <div class="h-96 content-center" v-if="isLoading">
                    <LucideLoaderCircle class="animate-spin mx-auto" :aria-label="t('pleaseWait')" />
                </div>
                <div class="h-96 flex flex-col items-center justify-center" v-else-if="isError">
                    <LucideAlertCircle class="mx-auto size-9" />
                    <p class="text-center mt-2 font-medium">{{ t("unknownError") }}</p>
                    <Button class="mx-auto mt-3" variant="outline" @click="refetch">
                        <RefreshCw />
                        {{ t("tryAgain") }}
                    </Button>
                </div>
                <div class="mt-4 border rounded-md shadow-xs overflow-hidden" v-else-if="sessions">
                    <table class="w-full">
                        <thead class="bg-accent not-md:sr-only">
                            <tr>
                                <td class="px-4 py-3 text-sm font-medium">{{ t("device") }}</td>
                                <td class="px-4 py-3 text-sm font-medium">{{ t("lastActivity") }}</td>
                                <td class="px-4 py-3 text-sm font-medium w-min">{{ t("actions") }}</td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                class="border-t not-md:first:border-none not-md:block"
                                v-for="(session, index) in sessions.sessions"
                                :key="index"
                            >
                                <td
                                    class="px-4 py-3 text-sm flex not-sm:flex-col sm:items-center items-start gap-2 not-md:pb-1"
                                >
                                    <LucideLaptop
                                        class="size-5"
                                        v-if="session.device.os && getDeviceTypeByOS(session.device.os) === 'desktop'"
                                        aria-label=""
                                    />
                                    <LucideSmartphone
                                        class="size-5"
                                        v-if="session.device.os && getDeviceTypeByOS(session.device.os) === 'mobile'"
                                    />
                                    <LucideHelpCircle class="size-5" v-else />
                                    <template v-if="session.device.name"> {{ session.device.name }}, </template>
                                    {{ session.device.os }}
                                    <template v-if="session.device.software">({{ session.device.software }}),</template>
                                    {{ t("ipAddress") }}: {{ session.device.ipAddress }}
                                    <span
                                        class="px-1.5 py-1 rounded-md bg-primary text-primary-foreground text-xs"
                                        v-if="session.id === sessions.currentSessionId"
                                    >
                                        {{ t("yourCurrentSession") }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 not-md:pt-0 text-sm text-foreground/70">
                                    <span class="md:hidden">{{ t("lastActivity") }}</span>
                                    {{ d(session.lastActivityDate, "hourMinute") }}
                                </td>
                                <td
                                    class="px-4 py-3 flex gap-2"
                                    :class="{ 'not-md:hidden': session.id === sessions.currentSessionId }"
                                >
                                    <RemoveSessionDialog
                                        :session-id="session.id"
                                        v-if="session.id !== sessions.currentSessionId"
                                    />
                                    <template v-else>-</template>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>
</template>
