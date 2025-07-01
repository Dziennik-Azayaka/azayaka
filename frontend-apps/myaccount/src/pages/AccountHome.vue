<script setup lang="ts">
import { LucideHelpCircle, LucideLaptop, LucideSmartphone } from "lucide-vue-next";
import { ref } from "vue";
import { useI18n } from "vue-i18n";

import EmailConfirmationBanner from "@/components/EmailConfirmationBanner.vue";
import PanelHeader from "@/components/PanelHeader.vue";

const { t, d } = useI18n();

const activeSessions = ref([
    {
        lastActiveDate: new Date(2025, 5, 13, 14, 55),
        device: {
            name: "Apple iPhone",
            os: "iOS 14.6",
            software: "Safari 14",
            ip: "192.168.1.101",
        },
        currentSession: true,
    },
    {
        lastActiveDate: new Date(2025, 5, 12, 13, 23),
        device: {
            name: null,
            os: "Linux",
            software: "Firefox 139",
            ip: "192.168.1.103",
        },
        currentSession: false,
    },
]);

const getDeviceTypeByOS = (os: string) => {
    if (os.startsWith("iOS") || os.startsWith("Android")) return "mobile";
    if (os.startsWith("Linux") || os.startsWith("Windows") || os.startsWith("macOS")) return "desktop";
    return "other";
};
</script>

<template>
    <div>
        <PanelHeader :breadcrumb-path="[{ href: '/', title: t('home') }]" />
        <main id="main-content">
            <h2 class="text-2xl font-semibold">{{ t("home") }}</h2>
            <p class="text-foreground/70 mb-4 text-sm">
                Witaj w ustawieniach konta. Możesz tu zmienić hasło, przejrzeć historię aktywności czy dodać nowe
                dostępy do dziennik Azayaka.
            </p>
            <EmailConfirmationBanner class="mb-3" />
            <section>
                <h3 class="font-semibold my-3">{{ t("activeSessions") }}</h3>
                <div class="mt-4 border rounded-md shadow-xs overflow-hidden">
                    <table class="w-full">
                        <thead class="bg-accent not-md:hidden">
                            <tr>
                                <td class="px-4 py-3 text-sm font-medium">{{ t("device") }}</td>
                                <td class="px-4 py-3 text-sm font-medium">{{ t("lastActivity") }}</td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                class="border-t not-md:first:border-none not-md:block"
                                v-for="(session, index) in activeSessions"
                                :key="index"
                            >
                                <td
                                    class="px-4 py-3 text-sm flex not-sm:flex-col sm:items-center items-start gap-2 not-md:pb-1"
                                >
                                    <LucideLaptop
                                        class="size-5"
                                        v-if="getDeviceTypeByOS(session.device.os) === 'desktop'"
                                    />
                                    <LucideSmartphone
                                        class="size-5"
                                        v-if="getDeviceTypeByOS(session.device.os) === 'mobile'"
                                    />
                                    <LucideHelpCircle
                                        class="size-5"
                                        v-if="getDeviceTypeByOS(session.device.os) === 'other'"
                                    />
                                    <template v-if="session.device.name"> {{ session.device.name }}, </template>
                                    {{ session.device.os }} ({{ session.device.software }}), {{ t("ipAddress") }}:
                                    {{ session.device.ip }}
                                    <span class="px-1.5 py-1 rounded-md bg-primary text-primary-foreground text-xs">
                                        {{ t("yourCurrentSession") }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 not-md:pt-0 text-sm text-foreground/70">
                                    <span class="md:hidden">{{ t("lastActivity") }}</span>
                                    {{ d(session.lastActiveDate, "hourMinute") }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>
</template>
