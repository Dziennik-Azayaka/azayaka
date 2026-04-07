<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { useUserStore } from '@/stores/user';
import { LucideChevronsUpDown } from 'lucide-vue-next';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';

const userStore = useUserStore();
const { t } = useI18n();

const initials = computed(() => (userStore.user ? userStore.user.email[0] : undefined));
</script>

<template>
  <Button size="lg" variant="ghost" class="h-[unset] p-2!">
    <div
      class="size-8 font-medium rounded-md bg-primary text-primary-foreground content-center text-center text-sm"
      aria-hidden="true"
    >
      {{ initials }}
    </div>
    <p class="font-medium text-sm not-md:hidden">{{ userStore.user?.email }}</p>
    <LucideChevronsUpDown
      class="size-4 not-md:hidden"
      :aria-label="t('common.actions.toggleMenu')"
    />
  </Button>
</template>
