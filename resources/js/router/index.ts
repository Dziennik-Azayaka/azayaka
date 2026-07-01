import { routes } from './routes';
import { useUserStore } from '@/stores/user';
import { createRouter, createWebHistory } from 'vue-router';

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach(async (to) => {
  const userStore = useUserStore();

  const user = await userStore.getUser();

  if ((to.meta.onlyLoggedIn || to.meta.onlyAdministrators) && !user) return { name: 'auth.logIn' };
  if (to.meta.onlyGuests && user) return { name: 'myAccount' };

  const accessId = to.params.accessId;
  const access = user?.accesses.find((access) => Number(accessId) === access.id);

  if (
    (to.meta.onlyAdministrators && (!access || !access.modulesAvailable.includes('administrator')))
    || (to.meta.onlySecretary && (!access || !access.modulesAvailable.includes('secretary')))
  ) return { name: 'myAccount' };

  if (access) userStore.access = access;

  return true;
});

export default router;
