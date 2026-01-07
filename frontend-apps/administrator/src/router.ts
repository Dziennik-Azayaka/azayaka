import { RouterView, createRouter, createWebHistory } from "vue-router";





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
            component: RouterView,
            children: [
                {
                    path: "",
                    name: "classes.list",
                    component: () => import("@/pages/ClassList.vue"),
                    meta: {
                        title: "classes",
                    },
                },
                {
                    path: ":id(\\d+)",
                    name: "classes.details",
                    component: () => import("@/pages/ClassDetails.vue"),
                    meta: {
                        title: "classDetails",
                    },
                    props: ({ params }) => ({ classId: Number(params.id) }),
                },
            ],
        },
        {
            path: "/:pathMatch(.*)*",
            name: "notFound",
            redirect: { name: "schoolStructure" },
        },
    ],
});

export default router;
