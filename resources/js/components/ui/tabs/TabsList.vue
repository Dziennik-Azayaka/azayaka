<script setup lang="ts">
import { cn } from '@/lib/utils';
import { reactiveOmit } from '@vueuse/core';
import type { TabsListProps } from 'reka-ui';
import { TabsList } from 'reka-ui';
import type { HTMLAttributes } from 'vue';

const props = defineProps<TabsListProps & { class?: HTMLAttributes['class'] }>();

const delegatedProps = reactiveOmit(props, 'class');
</script>

<template>
  <TabsList
    data-slot="tabs-list"
    v-bind="delegatedProps"
    :class="
      cn(
        'bg-accent text-muted-foreground inline-flex w-fit items-center justify-center rounded-lg p-1.5',
        props.class,
      )
    "
  >
    <slot />
  </TabsList>
</template>
