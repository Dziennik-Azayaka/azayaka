import { createRouter, createWebHistory } from "vue-router";

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
        {
            path: "/",
            name: "helloWorld",
            component: () => import("@/pages/HelloWorld.vue"),
            meta: {
                title: "helloWorld",
            },
        },
        {
            path: "/:pathMatch(.*)*",
            name: "notFound",
            redirect: { name: "helloWorld" },
        },
    ],
});

export default router;
