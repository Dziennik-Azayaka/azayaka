import type { RouteRecordRaw } from 'vue-router';

const route: RouteRecordRaw = {
  name: 'gradebook',
  path: '/teacher/:accessId/gradebook',
  component: () => import('./GradebookLayout.vue'),
  meta: { onlyTeacher: true },
  children: [
    {
      name: 'gradebook.view',
      path: '/teacher/:accessId/gradebook/class-unit/:classUnitId/school-year/:schoolYear',
      component: () => import('./pages/GradebookView.vue'),
      redirect: { name: 'gradebook.view.students' },
      meta: {
        breadcrumb: [
          { name: 'gradebook.title' },
          { name: 'gradebook.view.title', route: { name: 'gradebook.view' } },
        ],
      },
      children: [
        {
          name: 'gradebook.view.students',
          path: '/teacher/:accessId/gradebook/class-unit/:classUnitId/school-year/:schoolYear/students',
          component: () => import('./pages/GradebookStudentsTab.vue'),
        },
        {
          name: 'gradebook.view.groups',
          path: '/teacher/:accessId/gradebook/class-unit/:classUnitId/school-year/:schoolYear/groups',
          component: () => import('./pages/GradebookGroupsTab.vue'),
        },
        {
          name: 'gradebook.view.lessons',
          path: '/teacher/:accessId/gradebook/class-unit/:classUnitId/school-year/:schoolYear/lessons',
          component: () => import('./pages/GradebookLessonsTab.vue'),
        },
        {
          name: 'gradebook.view.attendance',
          path: '/teacher/:accessId/gradebook/class-unit/:classUnitId/school-year/:schoolYear/attendance',
          component: () => import('./pages/GradebookAttendanceTab.vue'),
        },
      ],
    },
  ],
};

export default route;
