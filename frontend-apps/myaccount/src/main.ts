import App from "./App.vue";
import i18n from "./i18n";
import "./index.css";
import router from "./router";
import { QueryClient, VueQueryPlugin } from "@tanstack/vue-query";
import { createPinia } from "pinia";
import { configure } from "vee-validate";
import { createApp } from "vue";

const app = createApp(App)
    .use(createPinia())
    .use(router)
    .use(i18n)
    .use(VueQueryPlugin, {
        queryClient: new QueryClient({
            defaultOptions: {
                queries: {
                    networkMode: "always",
                    retry: 0,
                },
                mutations: {
                    networkMode: "always",
                    retry: 0,
                },
            },
        }),
    });

configure({
    validateOnBlur: false,
    validateOnChange: false,
    validateOnInput: false,
    validateOnModelUpdate: false,
});

router.isReady().then(() => {
    app.mount("#app");
});
