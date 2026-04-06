<script setup lang="ts">
import PanelNavigationHeaderMenuTrigger from './PanelNavigationHeaderMenuTrigger.vue';
import { Button } from '@/components/ui/button';
import {
  Dialog,
  DialogClose,
  DialogContent,
  DialogFooter,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from '@/components/ui/dialog';
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { useUserStore } from '@/stores/user';
import { useMediaQuery } from '@vueuse/core';
import {
  LucideBookCopy,
  LucideBookMarked,
  LucideBuilding,
  LucideGraduationCap,
  LucideSettings2,
  LucideUserCog,
} from 'lucide-vue-next';
import { type Component } from 'vue';
import { useI18n } from 'vue-i18n';

type Module = 'myAccount' | 'administrator' | 'secretary' | 'register' | 'student' | 'teacher';

defineProps<{
  title: string;
}>();

const userStore = useUserStore();
const isMobile = useMediaQuery('(width < 80rem)');
const { t } = useI18n();

const moduleIcons: Record<Module, Component> = {
  myAccount: LucideUserCog,
  administrator: LucideSettings2,
  secretary: LucideBuilding,
  register: LucideBookCopy,
  student: LucideGraduationCap,
  teacher: LucideBookMarked,
};
</script>

<template>
  <!-- Desktop -->
  <DropdownMenu v-if="!isMobile">
    <DropdownMenuTrigger as-child>
      <PanelNavigationHeaderMenuTrigger :title="title" />
    </DropdownMenuTrigger>
    <DropdownMenuContent class="w-87.5">
      <DropdownMenuLabel>{{ t('goTo') }}</DropdownMenuLabel>
      <DropdownMenuItem as-child>
        <RouterLink
          :to="{ name: 'myAccount' }"
          target="_blank"
          :title="t('openInNewCardInfo')"
          class="cursor-pointer"
          active-class="bg-primary! text-primary-foreground! *:text-primary-foreground!"
        >
          <component :is="moduleIcons['myAccount']" class="text-foreground" />
          {{ t('myAccount.title') }}
        </RouterLink>
      </DropdownMenuItem>
      <template v-for="access in userStore.user?.accesses" :key="access.id">
        <p class="px-2 py-1.5 text-xs font-medium text-muted-foreground">
          {{ access.name }} ({{ t(access.type) }})
        </p>
        <DropdownMenuItem v-for="module in access.modulesAvailable" :key="module" as-child>
          <RouterLink
            :to="{
              name: module === 'administrator' ? module : 'auth.logIn',
              params: { accessId: access.id },
            }"
            target="_blank"
            :title="t('openInNewCardInfo')"
            class="cursor-pointer"
            active-class="bg-primary! text-primary-foreground! *:text-primary-foreground!"
          >
            <component :is="moduleIcons[module]" class="text-foreground" />
            {{ t(`${module}.title`) }}
          </RouterLink>
        </DropdownMenuItem>
      </template>
    </DropdownMenuContent>
  </DropdownMenu>

  <!-- Mobile -->
  <Dialog v-else>
    <DialogTrigger>
      <PanelNavigationHeaderMenuTrigger :title="title" />
    </DialogTrigger>
    <DialogContent>
      <DialogHeader>
        <DialogTitle>{{ t('goTo') }}</DialogTitle>
      </DialogHeader>
      <ul class="space-y-1.5">
        <li>
          <RouterLink
            :to="{ name: 'myAccount' }"
            target="_blank"
            :title="t('openInNewCardInfo')"
            class="px-4 py-3 rounded-md flex items-center gap-3 font-medium text-sm hover:bg-accent transition-colors"
          >
            <component :is="moduleIcons['myAccount']" class="text-foreground" />
            {{ t(`myAccount.title`) }}
          </RouterLink>
        </li>
        <li v-for="access in userStore.user?.accesses" :key="access.id">
          <p class="px-2 py-1.5 text-sm font-medium text-muted-foreground">
            {{ access.name }} ({{ t(access.type) }})
          </p>
          <ul class="space-y-0.5">
            <li v-for="module in access.modulesAvailable" :key="module">
              <RouterLink
                :to="{
                  name: module === 'administrator' ? module : 'auth.logIn',
                  params: { accessId: access.id },
                }"
                target="_blank"
                :title="t('openInNewCardInfo')"
                class="px-4 py-3 rounded-md flex items-center gap-3 font-medium text-sm hover:bg-accent transition-colors"
              >
                <component :is="moduleIcons[module]" class="text-foreground" />
                {{ t(`${module}.title`) }}
              </RouterLink>
            </li>
          </ul>
        </li>
      </ul>
      <DialogFooter>
        <DialogClose as-child>
          <Button variant="outline">{{ t('common.actions.close') }}</Button>
        </DialogClose>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>
