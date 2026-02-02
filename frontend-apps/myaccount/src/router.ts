import { useUserStore } from "./stores/user.store";
import { createRouter, createWebHistory } from "vue-router";

const router = createRouter({
    history: createWebHistory("/myaccount"),
    routes: [
        {
            path: "/",
            name: "home",
            component: () => import("@/pages/AccountHome.vue"),
            meta: {
                title: "home",
            },
        },
        {
            path: "/acitvity",
            name: "activity",
            component: () => import("@/pages/AccountActivity.vue"),
            meta: {
                title: "activityHistory",
            },
        },
        {
            path: "/data",
            name: "data",
            component: () => import("@/pages/AccountData.vue"),
            meta: {
                title: "accountData",
            },
        },
        {
            path: "/:pathMatch(.*)*",
            name: "notFound",
            redirect: { name: "home" },
        },
    ],
});

router.beforeEach(() => {
    const userStore = useUserStore();
    if (!userStore.user) userStore.fetchUser();
});

export default router;
