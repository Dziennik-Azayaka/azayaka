import {
  LucideBuilding,
  LucideCalendarCog,
  LucideLogIn,
  LucideShapes,
  LucideUsers,
} from 'lucide-vue-next';

export const menuItems = [
  {
    title: 'administrator.schoolStructure.title',
    link: { name: 'administrator.schoolStructure' },
    icon: LucideBuilding,
  },
  {
    title: 'administrator.employees.title',
    link: { name: 'administrator.employees' },
    icon: LucideUsers,
  },
  {
    title: 'administrator.systemAccess.title',
    link: { name: 'administrator.systemAccess' },
    icon: LucideLogIn,
  },
  {
    title: 'administrator.classificationPeriods.title',
    link: { name: 'administrator.classificationPeriods' },
    icon: LucideCalendarCog,
  },
  {
    title: 'administrator.subjects.title',
    link: { name: 'administrator.subjects' },
    icon: LucideShapes,
  },
];
