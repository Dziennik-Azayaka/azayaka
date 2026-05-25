<script setup lang="ts">
import { useCreateFulfillment } from '@/api/hooks/fulfillment/useCreateFulfillment';
import { useUpdateFulfillment } from '@/api/hooks/fulfillment/useUpdateFulfillment';
import { useDeleteFulfillment } from '@/api/hooks/fulfillment/useDeleteFulfillment';
import type { FulfillmentBody } from '@/api/services/fulfillment';
import type { CompulsoryEducationFulfillment } from '@/api/types/child';
import { ErrorBanner } from '@/components/ui/banner';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

import { computed, reactive, ref } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps<{
  fulfillments: CompulsoryEducationFulfillment[];
  childId: number;
}>();

const { t, d } = useI18n();

const createMutation = useCreateFulfillment();
const updateMutation = useUpdateFulfillment();
const deleteMutation = useDeleteFulfillment();

const editingId = ref<number | null>(null);
const isAdding = ref(false);
const deletingId = ref<number | null>(null);

const error = computed(
  () => createMutation.error.value ?? updateMutation.error.value ?? deleteMutation.error.value ?? null,
);

const isPending = computed(
  () =>
    createMutation.isPending.value ||
    updateMutation.isPending.value ||
    deleteMutation.isPending.value,
);

interface EditForm {
  schoolYear: number;
  controlDate: string;
  kindergartenInfo: string;
  postponementInfo: string;
  schoolInfo: string;
  outOfSchoolInfo: string;
  level: number;
}

const emptyForm = (): EditForm => ({
  schoolYear: new Date().getFullYear(),
  controlDate: '',
  kindergartenInfo: '',
  postponementInfo: '',
  schoolInfo: '',
  outOfSchoolInfo: '',
  level: 1,
});

const editForm = reactive<EditForm>(emptyForm());

function startEdit(f: CompulsoryEducationFulfillment) {
  editingId.value = f.id;
  isAdding.value = false;
  deletingId.value = null;
  editForm.schoolYear = f.schoolYear;
  editForm.controlDate = formatDate(f.controlDate);
  editForm.kindergartenInfo = f.kindergartenInfo ?? '';
  editForm.postponementInfo = f.postponementInfo ?? '';
  editForm.schoolInfo = f.schoolInfo ?? '';
  editForm.outOfSchoolInfo = f.outOfSchoolInfo ?? '';
  editForm.level = f.level;
}

function startAdd() {
  isAdding.value = true;
  editingId.value = null;
  deletingId.value = null;
  Object.assign(editForm, emptyForm());
}

function cancelEdit() {
  editingId.value = null;
  isAdding.value = false;
}

function formatDate(d: Date): string {
  return d.toISOString().slice(0, 10);
}

function toBody(): FulfillmentBody {
  return {
    schoolYear: editForm.schoolYear,
    controlDate: editForm.controlDate,
    kindergartenInfo: editForm.kindergartenInfo || null,
    postponementInfo: editForm.postponementInfo || null,
    schoolInfo: editForm.schoolInfo || null,
    outOfSchoolInfo: editForm.outOfSchoolInfo || null,
    level: editForm.level,
  };
}

function saveEdit() {
  if (editingId.value !== null) {
    updateMutation.mutate(
      { childId: props.childId, fulfillmentId: editingId.value, data: toBody() },
      { onSuccess: () => cancelEdit() },
    );
  }
}

function saveAdd() {
  createMutation.mutate(
    { childId: props.childId, data: toBody() },
    { onSuccess: () => cancelEdit() },
  );
}

function confirmDelete(id: number) {
  deletingId.value = id;
  editingId.value = null;
  isAdding.value = false;
}

function cancelDelete() {
  deletingId.value = null;
}

function doDelete(id: number) {
  deleteMutation.mutate(
    { childId: props.childId, fulfillmentId: id },
    { onSuccess: () => (deletingId.value = null) },
  );
}

function isEditing(id: number) {
  return editingId.value === id;
}

function isDeleting(id: number) {
  return deletingId.value === id;
}
</script>

<template>
  <div class="space-y-3 border-t pt-4">
    <div class="flex items-center justify-between">
      <h3 class="text-sm font-semibold">{{ t('secretary.childrenRegistry.fulfillment.title') }}</h3>
      <Button
        v-if="!isAdding && editingId === null"
        variant="outline"
        size="sm"
        type="button"
        @click="startAdd"
      >
        {{ t('secretary.childrenRegistry.fulfillment.addFulfillment') }}
      </Button>
    </div>

    <p
      v-if="fulfillments.length === 0 && !isAdding"
      class="text-sm text-muted-foreground"
    >
      {{ t('secretary.childrenRegistry.fulfillment.empty') }}
    </p>

    <ErrorBanner v-if="error" :error="error" />

    <!-- Existing fulfillments -->
    <div
      v-for="f in fulfillments"
      :key="f.id"
      class="rounded-md border p-3 space-y-2"
    >
      <!-- Delete confirmation -->
      <template v-if="isDeleting(f.id)">
        <p class="text-sm">{{ t('secretary.childrenRegistry.fulfillment.deleteConfirm') }}</p>
        <div class="flex gap-2">
          <Button variant="outline" size="sm" type="button" :disabled="isPending" @click="cancelDelete">
            {{ t('secretary.childrenRegistry.fulfillment.cancel') }}
          </Button>
          <Button variant="destructive" size="sm" type="button" :loading="isPending" @click="doDelete(f.id)">
            {{ t('secretary.childrenRegistry.fulfillment.delete') }}
          </Button>
        </div>
      </template>

      <!-- Edit mode -->
      <template v-else-if="isEditing(f.id)">
        <div class="grid sm:grid-cols-2 gap-2">
          <div>
            <Label>{{ t('secretary.childrenRegistry.fulfillment.schoolYear') }}</Label>
            <Input v-model.number="editForm.schoolYear" type="number" :disabled="isPending" />
          </div>
          <div>
            <Label>{{ t('secretary.childrenRegistry.fulfillment.controlDate') }}</Label>
            <Input v-model="editForm.controlDate" type="date" :disabled="isPending" />
          </div>
          <div>
            <Label>{{ t('secretary.childrenRegistry.fulfillment.level') }}</Label>
            <Input v-model.number="editForm.level" type="number" :disabled="isPending" />
          </div>
        </div>
        <div class="grid sm:grid-cols-2 gap-2">
          <div>
            <Label>{{ t('secretary.childrenRegistry.fulfillment.kindergartenInfo') }}</Label>
            <textarea class="flex min-h-[60px] w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50" v-model="editForm.kindergartenInfo" :disabled="isPending" rows="2" />
          </div>
          <div>
            <Label>{{ t('secretary.childrenRegistry.fulfillment.postponementInfo') }}</Label>
            <textarea class="flex min-h-[60px] w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50" v-model="editForm.postponementInfo" :disabled="isPending" rows="2" />
          </div>
          <div>
            <Label>{{ t('secretary.childrenRegistry.fulfillment.schoolInfo') }}</Label>
            <textarea class="flex min-h-[60px] w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50" v-model="editForm.schoolInfo" :disabled="isPending" rows="2" />
          </div>
          <div>
            <Label>{{ t('secretary.childrenRegistry.fulfillment.outOfSchoolInfo') }}</Label>
            <textarea class="flex min-h-[60px] w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50" v-model="editForm.outOfSchoolInfo" :disabled="isPending" rows="2" />
          </div>
        </div>
        <div class="flex gap-2">
          <Button variant="outline" size="sm" type="button" @click="cancelEdit">
            {{ t('secretary.childrenRegistry.fulfillment.cancel') }}
          </Button>
          <Button size="sm" type="button" :loading="isPending" @click="saveEdit">
            {{ t('secretary.childrenRegistry.fulfillment.save') }}
          </Button>
        </div>
      </template>

      <!-- View mode -->
      <template v-else>
        <div class="grid grid-cols-3 gap-2 text-sm">
          <div>
            <span class="text-muted-foreground">{{ t('secretary.childrenRegistry.fulfillment.schoolYear') }}:</span>
            {{ f.schoolYear }}
          </div>
          <div>
            <span class="text-muted-foreground">{{ t('secretary.childrenRegistry.fulfillment.controlDate') }}:</span>
            {{ d(f.controlDate, 'numericDate') }}
          </div>
          <div>
            <span class="text-muted-foreground">{{ t('secretary.childrenRegistry.fulfillment.level') }}:</span>
            {{ f.level }}
          </div>
        </div>
        <div v-if="f.kindergartenInfo" class="text-sm">
          <span class="text-muted-foreground">{{ t('secretary.childrenRegistry.fulfillment.kindergartenInfo') }}:</span>
          {{ f.kindergartenInfo }}
        </div>
        <div v-if="f.postponementInfo" class="text-sm">
          <span class="text-muted-foreground">{{ t('secretary.childrenRegistry.fulfillment.postponementInfo') }}:</span>
          {{ f.postponementInfo }}
        </div>
        <div v-if="f.schoolInfo" class="text-sm">
          <span class="text-muted-foreground">{{ t('secretary.childrenRegistry.fulfillment.schoolInfo') }}:</span>
          {{ f.schoolInfo }}
        </div>
        <div v-if="f.outOfSchoolInfo" class="text-sm">
          <span class="text-muted-foreground">{{ t('secretary.childrenRegistry.fulfillment.outOfSchoolInfo') }}:</span>
          {{ f.outOfSchoolInfo }}
        </div>
        <div class="flex gap-2">
          <Button variant="outline" size="sm" type="button" @click="startEdit(f)">
            {{ t('secretary.childrenRegistry.fulfillment.edit') }}
          </Button>
          <Button variant="destructive" size="sm" type="button" @click="confirmDelete(f.id)">
            {{ t('secretary.childrenRegistry.fulfillment.delete') }}
          </Button>
        </div>
      </template>
    </div>

    <!-- Add new fulfillment form -->
    <div v-if="isAdding" class="rounded-md border p-3 space-y-2">
      <div class="grid sm:grid-cols-2 gap-2">
        <div>
          <Label>{{ t('secretary.childrenRegistry.fulfillment.schoolYear') }}</Label>
          <Input v-model.number="editForm.schoolYear" type="number" :disabled="isPending" />
        </div>
        <div>
          <Label>{{ t('secretary.childrenRegistry.fulfillment.controlDate') }}</Label>
          <Input v-model="editForm.controlDate" type="date" :disabled="isPending" />
        </div>
        <div>
          <Label>{{ t('secretary.childrenRegistry.fulfillment.level') }}</Label>
          <Input v-model.number="editForm.level" type="number" :disabled="isPending" />
        </div>
      </div>
      <div class="grid sm:grid-cols-2 gap-2">
        <div>
          <Label>{{ t('secretary.childrenRegistry.fulfillment.kindergartenInfo') }}</Label>
          <textarea class="flex min-h-[60px] w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50" v-model="editForm.kindergartenInfo" :disabled="isPending" rows="2" />
        </div>
        <div>
          <Label>{{ t('secretary.childrenRegistry.fulfillment.postponementInfo') }}</Label>
          <textarea class="flex min-h-[60px] w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50" v-model="editForm.postponementInfo" :disabled="isPending" rows="2" />
        </div>
        <div>
          <Label>{{ t('secretary.childrenRegistry.fulfillment.schoolInfo') }}</Label>
          <textarea class="flex min-h-[60px] w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50" v-model="editForm.schoolInfo" :disabled="isPending" rows="2" />
        </div>
        <div>
          <Label>{{ t('secretary.childrenRegistry.fulfillment.outOfSchoolInfo') }}</Label>
          <textarea class="flex min-h-[60px] w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50" v-model="editForm.outOfSchoolInfo" :disabled="isPending" rows="2" />
        </div>
      </div>
      <div class="flex gap-2">
        <Button variant="outline" size="sm" type="button" @click="cancelEdit">
          {{ t('secretary.childrenRegistry.fulfillment.cancel') }}
        </Button>
        <Button size="sm" type="button" :loading="isPending" @click="saveAdd">
          {{ t('secretary.childrenRegistry.fulfillment.save') }}
        </Button>
      </div>
    </div>
  </div>
</template>
