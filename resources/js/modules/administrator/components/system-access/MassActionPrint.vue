<script setup lang="ts">
import { useDownloadAccessPdf } from '@/api/hooks/access/downloadAccessPdf';
import { AccessStatus, type AccessEntry, type AccessType } from '@/api/types/access';
import { Button } from '@/components/ui/button';
import {
  Dialog,
  DialogClose,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog';
import { useDownloadPDFPage } from '@/lib/utils';
import { LucidePrinter } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps<{
  type: AccessType;
  selected: AccessEntry[];
}>();

const { t } = useI18n();
const showWarning = ref(false);

const { mutate: download, isPending } = useDownloadAccessPdf();

function downloadPdf() {
  const { displayError, displayPDF } = useDownloadPDFPage();
  const ids = [
    ...new Set(
      props.selected
        .filter((access) => access.status === AccessStatus.CODE_GENERATED)
        .map((access) => access.entityId),
    ),
  ];

  download(
    { type: props.type, ids },
    {
      onSuccess: (blob) => {
        displayPDF(blob);
      },
      onError: () => {
        displayError();
      },
    },
  );
}

function onPrintClick() {
  if (props.selected.find((access) => access.status !== AccessStatus.CODE_GENERATED))
    showWarning.value = true;
  else downloadPdf();
}

const disabled = computed(
  () => !props.selected.find((access) => access.status === AccessStatus.CODE_GENERATED),
);
</script>

<template>
  <Button @click="onPrintClick()" :disabled="disabled" :loading="isPending">
    <LucidePrinter />
    {{ t('administrator.systemAccess.massActionPrint.title') }}
  </Button>

  <Dialog v-model:open="showWarning">
    <DialogContent>
      <DialogHeader>
        <DialogTitle>{{ t('administrator.systemAccess.massActionPrint.dialogTitle') }}</DialogTitle>
        <DialogDescription>
          {{ t('administrator.systemAccess.massActionPrint.dialogDescription') }}
        </DialogDescription>
      </DialogHeader>
      <DialogFooter>
        <DialogClose as-child>
          <Button variant="outline">{{ t('common.actions.close') }}</Button>
        </DialogClose>
        <DialogClose @click="downloadPdf" as-child>
          <Button>{{ t('common.actions.continue') }}</Button>
        </DialogClose>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>
