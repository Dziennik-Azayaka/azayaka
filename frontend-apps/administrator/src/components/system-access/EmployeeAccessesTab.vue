<script setup lang="ts">
import { onMounted, ref } from "vue";

import type { EmployeeAccessEntity } from "@/api/entities/employee-access.ts";
import AccessService from "@/api/services/access";

const loading = ref(true);
const error = ref(false);
const accesses = ref<EmployeeAccessEntity[] | null>();

async function getEmployeeAccesses() {
    loading.value = true;
    error.value = false;
    accesses.value = [];
    try {
        accesses.value = await AccessService.getEmployeeAccesses();
    } catch {
        error.value = true;
    } finally {
        loading.value = false;
    }
}

onMounted(getEmployeeAccesses);
</script>

<template>
    <pre class="p-5 rounded-md bg-accent font-mono text-xs">{{ accesses }}</pre>
</template>
