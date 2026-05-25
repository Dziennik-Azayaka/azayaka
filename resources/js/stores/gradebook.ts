import type { ClassUnit } from '@/api/types/class-unit';
import type { Gradebook } from '@/api/types/gradebook';
import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useGradebookStore = defineStore('gradebook', () => {
  const selectedGradebook = ref<Gradebook | null>(null);
  const selectedClassUnit = ref<ClassUnit | null>(null);
  const selectedSchoolYear = ref<number | null>(null);

  function setSelection(classUnit: ClassUnit, schoolYear: number) {
    selectedClassUnit.value = classUnit;
    selectedSchoolYear.value = schoolYear;
  }

  function setGradebook(gradebook: Gradebook | null) {
    selectedGradebook.value = gradebook;
  }

  return { selectedGradebook, selectedClassUnit, selectedSchoolYear, setSelection, setGradebook };
});
