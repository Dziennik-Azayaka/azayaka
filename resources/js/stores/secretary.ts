import { SchoolStructureService } from '@/api/services/school-structure';
import type { SchoolUnit } from '@/api/types/school-structure';
import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useSecretaryStore = defineStore('secretary', () => {
  const schoolUnits = ref<SchoolUnit[] | null>(null);
  const selectedUnit = ref<SchoolUnit | null>(null);

  async function setup() {
    if (!schoolUnits.value) schoolUnits.value = await SchoolStructureService.getSchoolUnits();
  }

  function switchUnit(id: number) {
    if (!schoolUnits.value) throw new Error("School units aren't loaded.");

    const unit = schoolUnits.value.find((unit) => unit.id === id);
    selectedUnit.value = unit ?? schoolUnits.value[0]!;

    return selectedUnit.value.id;
  }

  return { schoolUnits, selectedUnit, setup, switchUnit };
});
