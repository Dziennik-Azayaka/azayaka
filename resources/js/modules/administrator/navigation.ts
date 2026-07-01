import {
  LucideBuilding,
  LucideCalendarCog,
  LucideGrid2X2,
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
    title: 'administrator.classes.title',
    link: { name: 'administrator.classes.list' },
    icon: LucideGrid2X2,
  },
  {
    title: 'administrator.subjects.title',
    link: { name: 'administrator.subjects' },
    icon: LucideShapes,
  },
];
