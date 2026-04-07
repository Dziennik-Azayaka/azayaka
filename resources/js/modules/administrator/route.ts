import type { RouteRecordRaw } from 'vue-router';

const route: RouteRecordRaw = {
  name: 'administrator',
  path: '/administrator/:accessId',
  component: () => import('./AdministratorLayout.vue'),
  redirect: () => ({ name: 'administrator.schoolStructure' }),
  meta: { onlyAdministrators: true },
  children: [
    {
      name: 'administrator.schoolStructure',
      path: '/administrator/:accessId/school-structure',
      meta: {
        breadcrumb: [
          { name: 'administrator.title' },
          {
            name: 'administrator.schoolStructure.title',
            route: { name: 'administrator.schoolStructure' },
          },
        ],
      },
      component: () => import('./pages/SchoolStructure.vue'),
    },
    {
      name: 'administrator.employees',
      path: '/administrator/:accessId/employees',
      meta: {
        breadcrumb: [
          { name: 'administrator.title' },
          {
            name: 'administrator.employees.title',
            route: { name: 'administrator.employees' },
          },
        ],
      },
      component: () => import('./pages/EmployeeList.vue'),
    },
    {
      name: 'administrator.subjects',
      path: '/administrator/:accessId/subjects',
      meta: {
        breadcrumb: [
          { name: 'administrator.title' },
          {
            name: 'administrator.subjects.title',
            route: { name: 'administrator.subjects' },
          },
        ],
      },
      component: () => import('./pages/SubjectList.vue'),
    },
    {
      name: 'administrator.classificationPeriods',
      path: '/administrator/:accessId/classification-periods',
      meta: {
        breadcrumb: [
          { name: 'administrator.title' },
          {
            name: 'administrator.classificationPeriods.title',
            route: { name: 'administrator.classificationPeriods' },
          },
        ],
      },
      component: () => import('./pages/ClassificationPeriods.vue'),
    },
    {
      name: 'administrator.systemAccess',
      path: '/administrator/:accessId/system-access',
      meta: {
        breadcrumb: [
          { name: 'administrator.title' },
          {
            name: 'administrator.systemAccess.title',
            route: { name: 'administrator.systemAccess' },
          },
        ],
      },
      component: () => import('./pages/SystemAccess.vue'),
    },
    {
      name: 'administrator.classes',
      path: '/administrator/:accessId/classes',
      meta: {
        breadcrumb: [
          { name: 'administrator.title' },
          {
            name: 'administrator.classes.title',
            route: { name: 'administrator.classes' },
          },
        ],
      },
      component: () => import('./pages/ClassList.vue'),
    },
  ],
};

export default route;
