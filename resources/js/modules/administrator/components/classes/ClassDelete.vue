<script setup lang="ts">
import { useDeleteClass } from '@/api/hooks/classes/deleteClass';
import { ErrorBanner } from '@/components/ui/banner';
import { Button } from '@/components/ui/button';
import {
  Dialog,
  DialogClose,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from '@/components/ui/dialog';
import { LucideTrash2 } from 'lucide-vue-next';
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';
import { useRouter } from 'vue-router';

const props = defineProps<{ classId: number }>();

const { t } = useI18n();
const router = useRouter();
const dialogOpen = ref(false);

const { mutate: deleteClass, isPending, error } = useDeleteClass();

const onConfirm = () =>
  deleteClass(props.classId, {
    onSuccess: () => {
      router.push({ name: 'administrator.classes.list' });
    },
  });
</script>

<template>
  <Dialog v-model:open="dialogOpen">
    <DialogTrigger as-child>
      <Button variant="destructive">
        <LucideTrash2 />
        {{ t('common.actions.delete') }}
      </Button>
    </DialogTrigger>
    <DialogContent>
      <DialogHeader>
        <DialogTitle>{{ t('administrator.classes.delete.title') }}</DialogTitle>
        <DialogDescription>{{ t('administrator.classes.delete.description') }}</DialogDescription>
      </DialogHeader>

      <ErrorBanner v-if="error" :error="error" />

      <DialogFooter>
        <DialogClose as-child>
          <Button variant="outline" type="button">{{ t('common.actions.cancel') }}</Button>
        </DialogClose>
        <Button variant="destructive" :loading="isPending" @click="onConfirm">
          {{ t('common.actions.confirm') }}
        </Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>
