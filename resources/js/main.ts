import '../static/css/index.css';
import App from '@/App.vue';
import { i18n, loadI18nLocale } from '@/config/i18n';
import { queryClient, queryPlugin } from '@/config/tanstack-query';
import '@/config/valibot';
import '@/config/vee-validate';
import router from '@/router';
import { createPinia } from 'pinia';
import { createApp } from 'vue';

const app = createApp(App);

app.use(createPinia());
app.use(queryPlugin, { queryClient });
app.use(i18n);
app.use(router);

loadI18nLocale();

router.isReady().then(() => {
  app.mount('#app');
});
