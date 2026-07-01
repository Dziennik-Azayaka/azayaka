import type { SchoolUnit } from "@/api/types/school-structure";
import { BookCheck, BookUser } from "lucide-vue-next";
import type { Component } from "vue";
import type { RouteLocationAsRelativeGeneric } from "vue-router";

export function getMenuItems(unit: SchoolUnit) {
  const items: { title: string, icon: Component, link: RouteLocationAsRelativeGeneric }[] = [];

  items.push({
    icon: BookUser,
    title: unit.studentCategory === 'adultsOnly' ? 'secretary.listenerRegistry.title' : 'secretary.studentRegistry.title',
    link: { name: 'secretary.studentRegistry' }
  });

  if (unit.type === 25) items.push({
    icon: BookCheck,
    title: 'secretary.childrenRegistry.title',
    link: { name: 'secretary.childrenRegistry' }
  })

  return items;
}
