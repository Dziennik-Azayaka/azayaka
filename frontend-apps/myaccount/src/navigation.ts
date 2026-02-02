import { LucideHistory, LucideHome, LucideIdCard } from "lucide-vue-next";

export const menuItems = [
    {
        title: "home",
        link: { name: "home" },
        icon: LucideHome,
    },
    {
        title: "accountData",
        link: { name: "data" },
        icon: LucideIdCard,
    },
    {
        title: "activityHistory",
        link: { name: "activity" },
        icon: LucideHistory,
    },
];
