import administratorRoute from '@/modules/administrator/route';
import authenticationRoute from '@/modules/authentication/route';
import myAccountRoute from '@/modules/my-account/route';
import type { RouteRecordRaw } from 'vue-router';

export const routes: RouteRecordRaw[] = [
  authenticationRoute,
  myAccountRoute,
  administratorRoute,
  { path: '/', redirect: () => ({ name: 'myAccount' }) },
  { path: '/:pathMatch(.*)*', component: () => import('@/pages/NotFound.vue') },
];
