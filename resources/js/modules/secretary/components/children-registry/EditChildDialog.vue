<script setup lang="ts">
import { useDeleteChild } from '@/api/hooks/child/useDeleteChild';
import { useUpdatePerson } from '@/api/hooks/person/useUpdatePerson';
import type { CreatePersonBodyDTO } from '@/api/dtos/person';
import type { Child } from '@/api/types/child';
import { ErrorBanner } from '@/components/ui/banner';
import { Button } from '@/components/ui/button';
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from '@/components/ui/dialog';
import { computed, ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import ChildForm from './ChildForm.vue';
import FulfillmentSection from './FulfillmentSection.vue';
import type { ChildFormValues } from '../../forms/child';

const props = defineProps<{
  child: Child;
  schoolUnitId: number;
}>();

const { t } = useI18n();

const dialogOpen = ref(false);
const confirmingDelete = ref(false);

const updatePersonMutation = useUpdatePerson();
const deleteChildMutation = useDeleteChild();

const isSubmitting = computed(
  () =>
    updatePersonMutation.isPending.value ||
    deleteChildMutation.isPending.value,
);

const submitError = computed(
  () =>
    updatePersonMutation.error.value ??
    deleteChildMutation.error.value ??
    null,
);

function formatDate(d: Date): string {
  return d.toISOString().slice(0, 10);
}

const initialValues = computed<Partial<ChildFormValues>>(() => ({
  firstName: props.child.person.firstName,
  lastName: props.child.person.lastName,
  secondName: props.child.person.secondName ?? undefined,
  pesel: props.child.person.pesel ?? undefined,
  alternateIdentityDocument: props.child.person.alternateIdentityDocument ?? undefined,
  birthdate: formatDate(props.child.person.birthdate),
  birthplace: props.child.person.birthplace ?? '',
  gender: (props.child.person.gender as 'male' | 'female') ?? undefined,
  residenceAddressCountry: props.child.person.residenceAddress?.country ?? '',
  residenceAddressCommune: props.child.person.residenceAddress?.commune ?? undefined,
  residenceAddressTown: props.child.person.residenceAddress?.town ?? undefined,
  residenceAddressPostalCode: props.child.person.residenceAddress?.postalCode ?? undefined,
  residenceAddressStreet: props.child.person.residenceAddress?.street ?? undefined,
  residenceAddressHouseNumber: props.child.person.residenceAddress?.houseNumber ?? undefined,
  residenceAddressFlatNumber: props.child.person.residenceAddress?.flatNumber ?? undefined,
}));

async function onFormSubmit(values: ChildFormValues) {
  const personBody: CreatePersonBodyDTO = {
    firstName: values.firstName,
    lastName: values.lastName,
    secondName: values.secondName,
    pesel: values.pesel,
    alternateIdentityDocument: values.alternateIdentityDocument,
    birthdate: values.birthdate,
    birthplace: values.birthplace,
    gender: values.gender,
    residenceAddressCountry: values.residenceAddressCountry,
    residenceAddressCommune: values.residenceAddressCommune,
    residenceAddressTown: values.residenceAddressTown,
    residenceAddressPostalCode: values.residenceAddressPostalCode,
    residenceAddressStreet: values.residenceAddressStreet,
    residenceAddressHouseNumber: values.residenceAddressHouseNumber,
    residenceAddressFlatNumber: values.residenceAddressFlatNumber,
  };

  try {
    await updatePersonMutation.mutateAsync({
      schoolUnitId: props.schoolUnitId,
      personId: props.child.person.id,
      data: personBody,
    });
    dialogOpen.value = false;
  } catch {
    // error shown via submitError
  }
}

function onDeleteClick() {
  confirmingDelete.value = true;
}

function onDeleteConfirm() {
  deleteChildMutation.mutate(props.child.id, {
    onSuccess: () => {
      dialogOpen.value = false;
    },
  });
}

function cancelDelete() {
  confirmingDelete.value = false;
}

watch(dialogOpen, (val) => {
  if (!val) {
    confirmingDelete.value = false;
  }
});
</script>

<template>
  <Dialog v-model:open="dialogOpen">
    <DialogTrigger as-child>
      <slot />
    </DialogTrigger>
    <DialogContent class="sm:max-w-2xl">
      <DialogHeader>
        <DialogTitle>{{ t('secretary.childrenRegistry.editChildDialog.title') }}</DialogTitle>
        <DialogDescription>
          {{ t('secretary.childrenRegistry.editChildDialog.description') }}
        </DialogDescription>
      </DialogHeader>

      <template v-if="!confirmingDelete">
        <ChildForm
          :is-pending="isSubmitting"
          :initial-values="initialValues"
          :error="submitError"
          @submit="onFormSubmit"
        >
          <FulfillmentSection
            :fulfillments="child.compulsoryEducationFulfillments"
            :child-id="child.id"
          />
          <DialogFooter class="sm:justify-between">
            <Button variant="destructive" type="button" @click="onDeleteClick">
              {{ t('secretary.childrenRegistry.editChildDialog.delete') }}
            </Button>
            <div class="flex gap-2">
              <Button :loading="isSubmitting" type="submit">
                {{ t('common.actions.save') }}
              </Button>
            </div>
          </DialogFooter>
        </ChildForm>
      </template>

      <template v-else>
        <div class="space-y-3">
          <h3 class="text-lg font-semibold">
            {{ t('secretary.childrenRegistry.editChildDialog.deleteConfirm.title') }}
          </h3>
          <p class="text-sm text-muted-foreground">
            {{ t('secretary.childrenRegistry.editChildDialog.deleteConfirm.description') }}
          </p>
          <ErrorBanner v-if="deleteChildMutation.error.value" :error="deleteChildMutation.error.value" />
        </div>
        <DialogFooter>
          <Button variant="outline" @click="cancelDelete">
            {{ t('secretary.childrenRegistry.editChildDialog.deleteConfirm.cancel') }}
          </Button>
          <Button
            variant="destructive"
            :loading="deleteChildMutation.isPending.value"
            @click="onDeleteConfirm"
          >
            {{ t('secretary.childrenRegistry.editChildDialog.deleteConfirm.confirm') }}
          </Button>
        </DialogFooter>
      </template>
    </DialogContent>
  </Dialog>
</template>
