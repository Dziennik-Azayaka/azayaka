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
      redirect: () => ({ name: 'secretary.studentRegistery' }),
      children: [
        {
          name: 'secretary.studentRegistery',
          path: '/secretary/:accessId/school-units/:unitId/student-registery',
          component: () => import('./pages/StudentRegistery.vue'),
        },
        {
          name: 'secretary.childrenRegistery',
          path: '/secretary/:accessId/school-units/:unitId/children-registery',
          component: () => import('./pages/ChildrenRegistery.vue'),
          beforeEnter: (to) => {
            const secretaryStore = useSecretaryStore();
            if (secretaryStore.selectedUnit?.type !== 25)
              return { name: 'secretary.unit', params: to.params };
          },
        },
      ],
    },
  ],
};

export default route;
