import { GraduationCap, Users } from 'lucide-vue-next';
import { useRoute } from 'vue-router';
import type { Component } from 'vue';
import type { RouteLocationAsRelativeGeneric } from 'vue-router';

export function getMenuItems() {
  const route = useRoute();
  const items: { title: string; icon: Component; link: RouteLocationAsRelativeGeneric }[] = [];

  items.push({
    icon: Users,
    title: 'gradebook.tabs.students',
    link: {
      name: 'gradebook.view.students',
      params: { ...route.params },
    },
  });

  items.push({
    icon: GraduationCap,
    title: 'gradebook.tabs.groups',
    link: {
      name: 'gradebook.view.groups',
      params: { ...route.params },
    },
  });

  return items;
}
