import App from "./App.vue";
import i18n from "./i18n";
import "./index.css";
import router from "./router";
import { createPinia } from "pinia";
import { createApp } from "vue";

const app = createApp(App).use(createPinia()).use(router).use(i18n);

router.isReady().then(() => {
    app.mount("#app");
});
