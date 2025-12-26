import { useActivationStore } from "./stores/activation.store";
import { createRouter, createWebHistory } from "vue-router";

const router = createRouter({
    history: createWebHistory("/authentication"),
    routes: [
        {
            path: "/log-in",
            name: "logIn",
            component: () => import("@/pages/LogIn.vue"),
            meta: {
                title: "logIn",
            },
        },
        {
            path: "/reset-password",
            name: "resetPassword",
            component: () => import("@/pages/ResetPassword.vue"),
            meta: {
                title: "resetPassword",
            },
        },
        {
            path: "/reset-password/set-new",
            name: "resetPassword.setNew",
            component: () => import("@/pages/SetNewPassword.vue"),
            meta: {
                title: "resetPassword",
            },
        },
        {
            path: "/access-activation",
            children: [
                {
                    path: "/access-activation/code",
                    name: "activation.code",
                    component: () => import("@/pages/ActivationCode.vue"),
                    meta: {
                        title: "accessActivation",
                    },
                },
                {
                    path: "/access-activation/email-address",
                    name: "activation.emailAddress",
                    component: () => import("@/pages/ActivationEmailAddress.vue"),
                    meta: {
                        title: "accessActivation",
                    },
                },
                {
                    path: "/access-activation/log-in",
                    name: "activation.logIn",
                    component: () => import("@/pages/ActivationLogIn.vue"),
                    meta: {
                        title: "accessActivation",
                    },
                },
                {
                    path: "/access-activation/password",
                    name: "activation.setPassword",
                    component: () => import("@/pages/ActivationPassword.vue"),
                    meta: {
                        title: "accessActivation",
                    },
                },
                {
                    path: "/access-activation/email-confirmation",
                    name: "activation.emailAddressConfirmation",
                    component: () => import("@/pages/ActivationEmailAddressConfirmation.vue"),
                    meta: {
                        title: "accessActivation",
                    },
                },
            ],
            beforeEnter: async (to, from, next) => {
                const activationStore = useActivationStore();
                if (activationStore.needSync) await activationStore.syncWithApi();
                switch (activationStore.status.step) {
                    case "notStarted":
                        if (to.name !== "activation.code") next({ name: "activation.code" });
                        break;
                    case "code_found":
                        if (to.name !== "activation.emailAddress" && to.name !== "activation.code")
                            next({ name: "activation.emailAddress" });
                        break;
                }
                next();
            },
        },
        {
            path: "/:pathMatch(.*)*",
            name: "notFound",
            redirect: { name: "logIn" },
        },
    ],
});

export default router;
