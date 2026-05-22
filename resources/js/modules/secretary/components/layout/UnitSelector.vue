<script setup lang="ts">
import { Label } from '@/components/ui/label';
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select';
import { useSecretaryStore } from '@/stores/secretary';
import { useI18n } from 'vue-i18n';
import { useRoute, useRouter } from 'vue-router';

const secretaryStore = useSecretaryStore();
const { t } = useI18n();
const router = useRouter();
const route = useRoute();

async function handleUnitChange(unitId: number) {
  await router.push({ name: 'secretary.unit', params: { ...route.params, unitId } });
  secretaryStore.switchUnit(unitId);
}
</script>

<template>
  <div class="space-y-1.5 mx-2 mb-2" v-if="secretaryStore.selectedUnit && secretaryStore.schoolUnits">
    <Label>{{ t('common.data.schoolUnit') }}</Label>
    <Select @update:model-value="(v) => handleUnitChange(Number(v))">
      <SelectTrigger class="w-full bg-background">
        <SelectValue :class="{ italic: !secretaryStore.selectedUnit.active }">
          {{ secretaryStore.selectedUnit.name }}
          <template v-if="!secretaryStore.selectedUnit.active">
            ({{ t('administrator.schoolStructure.unitArchived') }})
          </template>
        </SelectValue>
      </SelectTrigger>
      <SelectContent>
        <SelectItem
          :class="{ italic: !unit.active }"
          v-for="unit in secretaryStore.schoolUnits"
          :key="unit.id"
          :value="unit.id"
        >
          {{ unit.name }}
          <template v-if="!unit.active">
            ({{ t('administrator.schoolStructure.unitArchived') }})
          </template>
        </SelectItem>
      </SelectContent>
    </Select>
  </div>
</template>
