import { createRouter, createWebHistory } from "vue-router";





const router = createRouter({
    history: createWebHistory("/administrator"),
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
            path: "/employees",
            name: "employees",
            component: () => import("@/pages/EmployeeList.vue"),
            meta: {
                title: "employees",
            },
        },
        {
            path: "/system-access",
            name: "systemAccess",
            component: () => import("@/pages/SystemAccess.vue"),
            meta: {
                title: "systemAccess",
            },
        },
        {
            path: "/subjects",
            name: "subjects",
            component: () => import("@/pages/SubjectList.vue"),
            meta: {
                title: "subjects",
            },
        },
        {
            path: "/classes",
            name: "classes",
            component: () => import("@/pages/ClassList.vue"),
            meta: {
                title: "classes",
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
