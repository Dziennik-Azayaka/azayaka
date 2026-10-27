<script setup lang="ts">
import AccessCode from './AccessCode.vue';
import AccessStatusInfo from './AccessStatusInfo.vue';
import { useDownloadAccessPdf } from '@/api/hooks/access/downloadAccessPdf';
import { useUpdateAccess } from '@/api/hooks/access/updateAccess';
import { AccessStatus, type AccessAction, type AccessEntry } from '@/api/types/access';
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
import { useDownloadPDFPage } from '@/lib/utils';
import { LucideLock, LucidePrinter, LucideRefreshCw, LucideRotateCw } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps<{ access: AccessEntry }>();

const { t, d } = useI18n();

const dialogOpen = ref(false);
const loading = ref<AccessAction | 'pdf' | null>(null);

const title = computed(() => {
  const typeName = t(`common.accessTypes.${props.access.type}`);
  const shortcut = props.access.secondary ? ` (${props.access.secondary})` : '';

  return `${props.access.fullName}${shortcut} - ${typeName}`;
});

const { error, mutate } = useUpdateAccess();
function update(action: AccessAction) {
  loading.value = action;
  mutate(
    { entry: props.access, action },
    {
      onSettled: () => {
        loading.value = null;
      },
    },
  );
}

const { mutate: download } = useDownloadAccessPdf();
function downloadPdf() {
  loading.value = 'pdf';
  const { displayError, displayPDF } = useDownloadPDFPage();

  download(
    { type: props.access.type, ids: [props.access.entityId] },
    {
      onSuccess: (blob) => {
        displayPDF(blob);
      },
      onError: () => {
        displayError();
      },
      onSettled: () => {
        loading.value = null;
      },
    },
  );
}
</script>

<template>
  <Dialog v-model:open="dialogOpen">
    <DialogTrigger as-child>
      <slot />
    </DialogTrigger>
    <DialogContent class="max-w-160!">
      <DialogHeader>
        <DialogTitle>{{ title }}</DialogTitle>
        <p v-if="access.studentName" class="text-sm text-muted-foreground">
          {{ t('administrator.systemAccess.student') }}: {{ access.studentName }}
        </p>
      </DialogHeader>

      <AccessStatusInfo :status="access.status" />
      <dl v-if="access.status === AccessStatus.ACTIVE" class="space-y-1 mt-2">
        <div class="space-y-0.5">
          <dt class="font-medium text-sm">{{ t('administrator.systemAccess.activationDate') }}</dt>
          <dd class="inline">{{ access.activatedAt ? d(access.activatedAt, 'long') : '-' }}</dd>
        </div>
        <div class="space-y-0.5">
          <dt class="font-medium text-sm">{{ t('administrator.systemAccess.lastLoginDate') }}</dt>
          <dd>{{ access.lastLoginAt ? d(access.lastLoginAt, 'long') : '-' }}</dd>
        </div>
      </dl>

      <AccessCode
        :code="access.activationCode ?? []"
        v-if="access.status === AccessStatus.CODE_GENERATED"
      />

      <p
        class="rounded-md px-4 py-3.5 bg-primary text-primary-foreground text-sm"
        v-if="access.status === AccessStatus.CODE_GENERATED"
      >
        {{ t('administrator.systemAccess.statusDescriptions.codeGenerated') }}
      </p>

      <p
        class="rounded-md px-4 py-3.5 bg-primary text-primary-foreground text-sm"
        v-if="access.status === AccessStatus.INACTIVE"
      >
        {{ t('administrator.systemAccess.statusDescriptions.noAccess') }}
      </p>

      <ErrorBanner :error="error" v-if="error" />

      <DialogFooter>
        <Button
          variant="outline"
          v-if="access.status === AccessStatus.CODE_GENERATED"
          type="button"
          @click="downloadPdf"
          :loading="loading === 'pdf'"
          :disabled="loading"
        >
          <LucidePrinter />
          {{ t('administrator.systemAccess.actions.printInstructionsOne') }}
        </Button>
        <Button
          variant="default"
          type="button"
          v-if="access.status === AccessStatus.INACTIVE"
          @click="update('generate')"
          :loading="loading === 'generate'"
          :disabled="loading"
        >
          <LucideRefreshCw />
          {{ t('administrator.systemAccess.actions.generate') }}
        </Button>
        <Button
          variant="outline"
          type="button"
          v-if="access.status === AccessStatus.ACTIVE"
          @click="update('regenerate')"
          :loading="loading === 'regenerate'"
          :disabled="loading"
        >
          <LucideRotateCw />
          {{ t('administrator.systemAccess.actions.regenerate') }}
        </Button>
        <Button
          variant="destructive"
          type="button"
          v-if="access.type === 'employee' && access.status === AccessStatus.ACTIVE"
          @click="update('revoke')"
          :loading="loading === 'revoke'"
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
