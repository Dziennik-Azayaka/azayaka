import { useSecretaryStore } from '@/stores/secretary';
import { RouterView, type RouteRecordRaw } from 'vue-router';

const route: RouteRecordRaw = {
  name: 'secretary',
  path: '/secretary/:accessId',
  component: () => import('./SecretaryLayout.vue'),
  meta: { onlySecretary: true },
  beforeEnter: async (to) => {
    const secretaryStore = useSecretaryStore();
    await secretaryStore.setup();

    if (to.name === 'secretary')
      return {
        name: 'secretary.unit',
        params: { ...to.params, unitId: secretaryStore.schoolUnits![0]!.id },
      };
  },
  children: [
    {
      name: 'secretary.unit',
      path: '/secretary/:accessId/school-units/:unitId',
      component: RouterView,
      beforeEnter: (to) => {
        const secretaryStore = useSecretaryStore();
        const idParam = Number(to.params?.unitId);
        const idSet = secretaryStore.switchUnit(idParam);

        if (idParam !== idSet) return { name: to.name, params: { ...to.params, unitId: idSet } };
      },
      redirect: () => ({ name: 'secretary.studentRegistry' }),
      children: [
        {
          name: 'secretary.studentRegistry',
          path: '/secretary/:accessId/school-units/:unitId/student-registry',
          component: () => import('./pages/StudentRegistry.vue'),
          meta: {
            breadcrumb: [
              { name: 'secretary.title' },
              {
                name: 'secretary.studentRegistry.title',
                route: { name: 'secretary.studentRegistry' },
              },
            ],
          },
        },
        {
          name: 'secretary.childrenRegistry',
          path: '/secretary/:accessId/school-units/:unitId/children-registry',
          component: () => import('./pages/ChildrenRegistry.vue'),
          beforeEnter: (to) => {
            const secretaryStore = useSecretaryStore();
            if (secretaryStore.selectedUnit?.type !== 25)
              return { name: 'secretary.unit', params: to.params };
          },
          meta: {
            breadcrumb: [
              { name: 'secretary.title' },
              {
                name: 'secretary.childrenRegistry.title',
                route: { name: 'secretary.childrenRegistry' },
              },
            ],
          },
        },
      ],
    },
  ],
};

export default route;
