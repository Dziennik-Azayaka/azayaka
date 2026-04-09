import { useActivationStore } from '@/stores/activation.ts';
import { type RouteRecordRaw } from 'vue-router';

const authenticationRoute: RouteRecordRaw = {
  name: 'auth',
  path: '/authentication',
  component: () => import('./AuthenticationLayout.vue'),
  redirect: () => ({ name: 'auth.logIn' }),
  children: [
    {
      name: 'auth.logIn',
      path: '/authentication/log-in',
      component: () => import('./pages/LogIn.vue'),
      meta: { onlyGuests: true },
    },
    {
      name: 'auth.activation',
      path: '/authentication/access-activation',
      redirect: { name: 'auth.activation.code' },
      children: [
        {
          name: 'auth.activation.code',
          path: '/authentication/access-activation/code',
          component: () => import('./pages/ActivationCode.vue'),
        },
        {
          name: 'auth.activation.email',
          path: '/authentication/access-activation/email',
          component: () => import('./pages/ActivationEmail.vue'),
          beforeEnter: async () => {
            const activationStore = useActivationStore();
            if (activationStore.needSync) await activationStore.syncWithApi();
            if (activationStore.status.step === 'not_started')
              return { name: 'auth.activation.code' };
          },
        },
        {
          name: 'auth.activation.setPassword',
          path: '/authentication/access-activation/set-password',
          component: () => import('./pages/ActivationPassword.vue'),
          beforeEnter: async () => {
            const activationStore = useActivationStore();
            if (activationStore.needSync) await activationStore.syncWithApi();
            if (activationStore.status.step !== 'email_available')
              return { name: 'auth.activation.email' };
          },
        },
        {
          name: 'auth.activation.logIn',
          path: '/authentication/access-activation/log-in',
          component: () => import('./pages/ActivationLogIn.vue'),
          beforeEnter: async () => {
            const activationStore = useActivationStore();
            if (activationStore.needSync) await activationStore.syncWithApi();
            if (activationStore.status.step !== 'attach_to_account')
              return { name: 'auth.activation.email' };
          },
        },
      ],
    },
  ],
};

export default authenticationRoute;
