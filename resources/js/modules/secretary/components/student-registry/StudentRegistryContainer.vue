<script setup lang="ts">
import { useGetFilteredStudents } from '@/api/hooks/getFilteredStudents';
import { ref } from 'vue';

const props = defineProps<{ registryId: number }>();

const gender = ref(null);
const birthYear = ref(null);
const status = ref(null);

const { data } = useGetFilteredStudents(
  props.registryId,
  birthYear,
  gender,
  status,
);
</script>

<template>
  <pre class="p-2 rounded-md bg-accent font-mono text-xs">{{ data }}</pre>
</template>
