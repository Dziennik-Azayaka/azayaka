import { ActivationService } from '@/api/services/activation';
import type { ActivationStatus } from '@/api/types/activation-status';
import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useActivationStore = defineStore('activation', () => {
  const needSync = ref(true);
  const status = ref<ActivationStatus>({ step: 'not_started' });

  async function syncWithApi() {
    status.value = await ActivationService.getStatus();
    needSync.value = false;
  }

  return { status, needSync, syncWithApi };
});
