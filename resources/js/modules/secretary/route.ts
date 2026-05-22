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

    if (to.name === 'secretary') return { name: 'secretary.unit', params: { ...to.params, unitId: secretaryStore.schoolUnits![0]!.id } }
  },
  children: [
    {
      name: 'secretary.unit',
      path: '/secretary/:accessId/school-units/:unitId',
      component: RouterView,
      beforeEnter: (to) => {
        const secretaryStore = useSecretaryStore();
        const idParam = Number(to.params.unitId);
        const idSet = secretaryStore.switchUnit(idParam);

        console.log(idParam, idSet)

        if (idParam !== idSet) return { name: to.name, params: { ...to.params, unitId: idSet } };
      }
    }
  ]
};

export default route;
