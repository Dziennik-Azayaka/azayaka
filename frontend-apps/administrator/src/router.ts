import { createRouter, createWebHistory } from "vue-router";

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
        {
            path: "/",
            name: "schoolStructure",
            component: () => import("@/pages/SchoolStructure.vue"),
            meta: {
                title: "schoolStructure",
            },
        },
        {
            path: "/:pathMatch(.*)*",
            name: "notFound",
            redirect: { name: "schoolStructure" },
        },
    ],
});

export default router;
