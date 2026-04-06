<script setup lang="ts">
import AccessCode from './AccessCode.vue';
import AccessStatusInfo from './AccessStatusInfo.vue';
import { useDownloadEmployeeAccessPdf } from '@/api/hooks/employee/downloadEmployeeAccessPdf';
import { useUpdateEmployeeAccess } from '@/api/hooks/employee/updateEmployeeAccess';
import { AccessStatus } from '@/api/types/access';
import type { EmployeeAccess } from '@/api/types/employee-access';
import { ErrorBanner } from '@/components/ui/banner';
import { Button } from '@/components/ui/button';
import {
  DialogClose,
  DialogFooter,
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from '@/components/ui/dialog';
import { LucideLock, LucidePrinter, LucideRefreshCw, LucideRotateCw } from 'lucide-vue-next';
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps<{
  data: EmployeeAccess;
  accessType: 'employee' | 'guardian' | 'student';
}>();

const { t, d } = useI18n();

const dialogOpen = ref(false);
const loading = ref<'regenerate' | 'generate' | 'revoke' | 'pdf' | null>(null);

const { error, mutate } = useUpdateEmployeeAccess();
function update(action: 'revoke' | 'regenerate' | 'generate') {
  loading.value = action;
  mutate(
    { id: props.data.id, action },
    {
      onSettled: () => {
        loading.value = null;
      },
    },
  );
}

const { mutate: download } = useDownloadEmployeeAccessPdf();
function downloadPdf() {
  loading.value = 'pdf';
  download([props.data.id], {
    onSuccess: (blob) => {
      const url = window.URL.createObjectURL(blob);
      window.open(url, '_blank');
      setTimeout(() => window.URL.revokeObjectURL(url), 60000);

      loading.value = null;
    },
  });
}
</script>

<template>
  <Dialog v-model:open="dialogOpen">
    <DialogTrigger as-child>
      <slot />
    </DialogTrigger>
    <DialogContent class="max-w-160!">
      <DialogHeader>
        <DialogTitle>
          <template v-if="accessType === 'employee'">
            {{ data.fullName }} ({{ data.shortcut }}) - {{ t('common.accessTypes.employee') }}
          </template>
        </DialogTitle>
      </DialogHeader>

      <AccessStatusInfo :status="data.status" />
      <dl v-if="data.status === AccessStatus.ACTIVE" class="space-y-1 mt-2">
        <div class="space-y-0.5">
          <dt class="font-medium text-sm">{{ t('administrator.systemAccess.activationDate') }}</dt>
          <dd class="inline">{{ d(data.activatedAt, 'long') }}</dd>
        </div>
        <div class="space-y-0.5">
          <dt class="font-medium text-sm">{{ t('administrator.systemAccess.lastLoginDate') }}</dt>
          <dd>{{ d(data.lastLoginAt, 'long') }}</dd>
        </div>
      </dl>

      <AccessCode :code="data.activationCode" v-if="data.status === AccessStatus.CODE_GENERATED" />

      <p
        class="rounded-md px-4 py-3.5 bg-primary text-primary-foreground text-sm"
        v-if="data.status === AccessStatus.CODE_GENERATED"
      >
        {{ t('administrator.systemAccess.statusDescriptions.codeGenerated') }}
      </p>

      <p
        class="rounded-md px-4 py-3.5 bg-primary text-primary-foreground text-sm"
        v-if="data.status === AccessStatus.INACTIVE"
      >
        {{ t('administrator.systemAccess.statusDescriptions.inactive') }}
      </p>

      <ErrorBanner :error="error" v-if="error" />

      <DialogFooter>
        <Button
          variant="outline"
          v-if="data.status === AccessStatus.CODE_GENERATED"
          type="button"
          @click="downloadPdf"
        >
          <LucidePrinter />
          {{ t('administrator.systemAccess.actions.printInstructionsOne') }}
        </Button>
        <Button
          variant="default"
          type="button"
          v-if="data.status === AccessStatus.INACTIVE"
          @click="update('generate')"
          :disabled="loading"
        >
          <LucideRefreshCw />
          {{ t('administrator.systemAccess.actions.generate') }}
        </Button>
        <Button
          variant="outline"
          type="button"
          v-if="data.status === AccessStatus.ACTIVE"
          @click="update('regenerate')"
          :disabled="loading"
        >
          <LucideRotateCw />
          {{ t('administrator.systemAccess.actions.regenerate') }}
        </Button>
        <Button
          variant="destructive"
          type="button"
          v-if="data.status !== AccessStatus.INACTIVE"
          @click="update('revoke')"
          :disabled="loading"
        >
          <LucideLock />
          {{ t('administrator.systemAccess.actions.revoke') }}
        </Button>
        <div class="flex-1"></div>
        <DialogClose as-child>
          <Button variant="outline" type="button"> {{ t('common.actions.close') }} </Button>
        </DialogClose>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>
