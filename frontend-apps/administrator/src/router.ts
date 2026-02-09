import { useUserStore } from "./stores/user.store";
import { RouterView, createRouter, createWebHistory } from "vue-router";

const router = createRouter({
    history: createWebHistory("/administrator"),
    routes: [
        {
            path: "/:accessId(\\d+)",
            component: RouterView,
			redirect: { name: "schoolStructure" },
            children: [
                {
                    path: "school-structure",
                    name: "schoolStructure",
                    component: () => import("@/pages/SchoolStructure.vue"),
                    meta: {
                        title: "schoolStructure",
                    },
                },
                {
                    path: "employees",
                    name: "employees",
                    component: () => import("@/pages/EmployeeList.vue"),
                    meta: {
                        title: "employees",
                    },
                },
                {
                    path: "system-access",
                    name: "systemAccess",
                    component: () => import("@/pages/SystemAccess.vue"),
                    meta: {
                        title: "systemAccess",
                    },
                },
                {
                    path: "subjects",
                    name: "subjects",
                    component: () => import("@/pages/SubjectList.vue"),
                    meta: {
                        title: "subjects",
                    },
                },
                {
                    path: ":pathMatch(.*)*",
                    name: "notFound",
                    redirect: { name: "schoolStructure" },
                },
            ],
        },
		{
            path: "/:accessId(\\d+)/classes",
			name: "classes",
            component: RouterView,
			redirect: { name: "classes.list" },
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
                        title: "classes",
                    },
					props: ({ params }) => ({ classId: Number(params.id) }),
                },
                {
                    path: ":pathMatch(.*)*",
                    name: "notFound",
                    redirect: { name: "schoolStructure" },
                },
            ],
        },
    ],
});

router.beforeEach(async (to) => {
    const userStore = useUserStore();

    const user = await userStore.getUser();
    const accessId = Number(to.params.accessId);
    const access = user.accesses.find((access) => access.id === accessId);

    if (!access) {
        console.warn(`Access with id = ${accessId} not found!`);
        window.location.href = "/";
        return;
    }

    userStore.setAccess(access);

	console.log(userStore.access);
	return true;
});

export default router;
