import { type RouteRecordRaw } from 'vue-router';

const myAccountRoute: RouteRecordRaw = {
  name: 'myAccount',
  path: '/my-account',
  component: () => import('./MyAccountLayout.vue'),
  redirect: () => ({ name: 'myAccount.home' }),
  meta: { onlyLoggedIn: true },
  children: [
    {
      name: 'myAccount.home',
      path: '/my-account/home',
      component: () => import('./pages/AccountHome.vue'),
      meta: {
        breadcrumb: [
          { name: 'myAccount.title' },
          { name: 'myAccount.home.title', route: { name: 'myAccount.home' } },
        ],
      },
    },
    {
      name: 'myAccount.data',
      path: '/my-account/data',
      component: () => import('./pages/AccountData.vue'),
      meta: {
        breadcrumb: [
          { name: 'myAccount.title' },
          { name: 'myAccount.accountData.title', route: { name: 'myAccount.data' } },
        ],
      },
    },
    {
      name: 'myAccount.activity',
      path: '/my-account/activity',
      component: () => import('./pages/AccountActivity.vue'),
      meta: {
        breadcrumb: [
          { name: 'myAccount.title' },
          { name: 'myAccount.activityHistory.title', route: { name: 'myAccount.activity' } },
        ],
      },
    },
  ],
};

export default myAccountRoute;
