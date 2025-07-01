import { createRouter, createWebHistory } from "vue-router";

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
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
            name: "activation",
            redirect: {
                name: "activation.code",
                replace: true,
            },
        },
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
        {
            path: "/:pathMatch(.*)*",
            name: "notFound",
            redirect: { name: "logIn" },
        },
    ],
});

export default router;
