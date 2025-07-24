<script setup lang="ts">
import type { BasicColorSchema } from "@vueuse/core";
import { LucideALargeSmall, LucideContrast, LucideLanguages, LucideLogOut, LucideSettings } from "lucide-vue-next";
import { useI18n } from "vue-i18n";

import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuPortal,
    DropdownMenuRadioGroup,
    DropdownMenuRadioItem,
    DropdownMenuSeparator,
    DropdownMenuSub,
    DropdownMenuSubContent,
    DropdownMenuSubTrigger,
    DropdownMenuTrigger,
} from "#ui/components/ui/dropdown-menu";
import PanelAccountMenuTrigger from "#ui/layouts/panel/PanelAccountMenuTrigger.vue";

const theme = defineModel<BasicColorSchema | "highContrast">("theme", { required: true });
const lang = defineModel<string>("lang", { required: true });
const fontSize = defineModel<"normal" | "large">("fontSize", { required: true });
const props = defineProps<{
    emailAddress: string;
}>();
const { t } = useI18n();
</script>

<template>
    <DropdownMenu>
        <DropdownMenuTrigger as-child>
            <PanelAccountMenuTrigger
                :email-address="props.emailAddress"
                :initials="props.emailAddress[0].toUpperCase()"
            />
        </DropdownMenuTrigger>
        <DropdownMenuContent>
            <DropdownMenuLabel>{{ emailAddress }}</DropdownMenuLabel>
            <DropdownMenuSeparator />

            <DropdownMenuSub>
                <DropdownMenuSubTrigger>
                    <LucideContrast class="size-4 text-muted-foreground mr-2" aria-hidden="true" />
                    {{ t("changeTheme") }}
                </DropdownMenuSubTrigger>
                <DropdownMenuPortal>
                    <DropdownMenuSubContent>
                        <DropdownMenuRadioGroup v-model="theme">
                            <DropdownMenuRadioItem value="light">{{ t("light") }}</DropdownMenuRadioItem>
                            <DropdownMenuRadioItem value="dark">{{ t("dark") }}</DropdownMenuRadioItem>
                            <DropdownMenuRadioItem value="high-contrast">{{ t("highContrast") }}</DropdownMenuRadioItem>
                        </DropdownMenuRadioGroup>
                    </DropdownMenuSubContent>
                </DropdownMenuPortal>
            </DropdownMenuSub>

            <DropdownMenuSub>
                <DropdownMenuSubTrigger>
                    <LucideLanguages class="size-4 text-muted-foreground mr-2" aria-hidden="true" />
                    {{ t("changeLanguage") }}
                </DropdownMenuSubTrigger>
                <DropdownMenuPortal>
                    <DropdownMenuSubContent>
                        <DropdownMenuRadioGroup v-model="lang">
                            <DropdownMenuRadioItem value="pl">Polski (pl)</DropdownMenuRadioItem>
                            <DropdownMenuRadioItem value="en">English (en)</DropdownMenuRadioItem>
                        </DropdownMenuRadioGroup>
                    </DropdownMenuSubContent>
                </DropdownMenuPortal>
            </DropdownMenuSub>
            <DropdownMenuSub>
                <DropdownMenuSubTrigger>
                    <LucideALargeSmall class="size-4 text-muted-foreground mr-2" aria-hidden="true" />
                    {{ t("changeFontSize") }}
                </DropdownMenuSubTrigger>
                <DropdownMenuPortal>
                    <DropdownMenuSubContent>
                        <DropdownMenuRadioGroup v-model="fontSize">
                            <DropdownMenuRadioItem value="normal">{{ t("normal") }}</DropdownMenuRadioItem>
                            <DropdownMenuRadioItem value="large">{{ t("larger") }}</DropdownMenuRadioItem>
                        </DropdownMenuRadioGroup>
                    </DropdownMenuSubContent>
                </DropdownMenuPortal>
            </DropdownMenuSub>
            <DropdownMenuItem as-child>
                <a href="/myaccount">
                    <LucideSettings aria-hidden="true" />
                    {{ t("accountSettings") }}
                </a>
            </DropdownMenuItem>
            <DropdownMenuSeparator />
            <DropdownMenuItem variant="destructive">
                <LucideLogOut />
                {{ t("logOut") }}
            </DropdownMenuItem>
        </DropdownMenuContent>
    </DropdownMenu>
</template>
