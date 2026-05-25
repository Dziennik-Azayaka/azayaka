<script setup lang="ts">
import { useDeleteStudent } from '@/api/hooks/student/useDeleteStudent';
import { useUpdatePerson } from '@/api/hooks/person/useUpdatePerson';
import { useUpdateStudent } from '@/api/hooks/student/useUpdateStudent';
import type { CreatePersonBodyDTO } from '@/api/dtos/person';
import type { Student } from '@/api/types/student';
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
import StudentForm from './StudentForm.vue';
import type { EditStudentFormValues } from '../../forms/editStudent';

const props = defineProps<{
  student: Student;
  schoolUnitId: number;
}>();

const { t } = useI18n();

const dialogOpen = ref(false);
const confirmingDelete = ref(false);

const updatePersonMutation = useUpdatePerson();
const updateStudentMutation = useUpdateStudent();
const deleteStudentMutation = useDeleteStudent();

const isSubmitting = computed(
  () =>
    updatePersonMutation.isPending.value ||
    updateStudentMutation.isPending.value ||
    deleteStudentMutation.isPending.value,
);

const submitError = computed(
  () =>
    updatePersonMutation.error.value ??
    updateStudentMutation.error.value ??
    deleteStudentMutation.error.value ??
    null,
);

function formatDate(d: Date): string {
  const iso = d.toISOString();
  return iso.slice(0, 10);
}

const initialValues = computed<Partial<EditStudentFormValues>>(() => ({
  firstName: props.student.person.firstName,
  lastName: props.student.person.lastName,
  secondName: props.student.person.secondName ?? undefined,
  pesel: props.student.person.pesel ?? undefined,
  alternateIdentityDocument: props.student.person.alternateIdentityDocument ?? undefined,
  birthdate: formatDate(props.student.person.birthdate),
  birthplace: props.student.person.birthplace ?? '',
  gender: (props.student.person.gender as 'male' | 'female') ?? undefined,
  residenceAddressCountry: props.student.person.residenceAddress?.country ?? '',
  residenceAddressCommune: props.student.person.residenceAddress?.commune ?? undefined,
  residenceAddressTown: props.student.person.residenceAddress?.town ?? undefined,
  residenceAddressPostalCode: props.student.person.residenceAddress?.postalCode ?? undefined,
  residenceAddressStreet: props.student.person.residenceAddress?.street ?? undefined,
  residenceAddressHouseNumber: props.student.person.residenceAddress?.houseNumber ?? undefined,
  residenceAddressFlatNumber: props.student.person.residenceAddress?.flatNumber ?? undefined,
  admissionDate: formatDate(props.student.admissionDate),
  leaveDate: props.student.leaveDate ? formatDate(props.student.leaveDate) : undefined,
  leaveReason: props.student.leaveReason ?? undefined,
}));

async function onFormSubmit(values: EditStudentFormValues) {
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
    studentRegistryId: 0,
    admissionDate: values.admissionDate,
  };

  const studentBody = {
    admissionDate: values.admissionDate,
    leaveDate: values.leaveDate,
    leaveReason: values.leaveReason,
  };

  try {
    await Promise.all([
      updatePersonMutation.mutateAsync({
        schoolUnitId: props.schoolUnitId,
        personId: props.student.person.id,
        data: personBody,
      }),
      updateStudentMutation.mutateAsync({
        id: props.student.id,
        body: studentBody,
      }),
    ]);
    dialogOpen.value = false;
  } catch {
    // error shown via submitError
  }
}

function onDeleteClick() {
  confirmingDelete.value = true;
}

function onDeleteConfirm() {
  deleteStudentMutation.mutate(props.student.id, {
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
        <DialogTitle>{{ t('secretary.studentRegistry.editStudentDialog.title') }}</DialogTitle>
        <DialogDescription>
          {{ t('secretary.studentRegistry.editStudentDialog.description') }}
        </DialogDescription>
      </DialogHeader>

      <template v-if="!confirmingDelete">
        <StudentForm
          :is-pending="isSubmitting"
          :initial-values="initialValues"
          :error="submitError"
          @submit="onFormSubmit"
        >
          <DialogFooter class="sm:justify-between">
            <Button variant="destructive" type="button" @click="onDeleteClick">
              {{ t('secretary.studentRegistry.editStudentDialog.delete') }}
            </Button>
            <div class="flex gap-2">
              <Button :loading="isSubmitting" type="submit">
                {{ t('common.actions.save') }}
              </Button>
            </div>
          </DialogFooter>
        </StudentForm>
      </template>

      <template v-else>
        <div class="space-y-3">
          <h3 class="text-lg font-semibold">
            {{ t('secretary.studentRegistry.editStudentDialog.deleteConfirm.title') }}
          </h3>
          <p class="text-sm text-muted-foreground">
            {{ t('secretary.studentRegistry.editStudentDialog.deleteConfirm.description') }}
          </p>
          <ErrorBanner v-if="deleteStudentMutation.error.value" :error="deleteStudentMutation.error.value" />
        </div>
        <DialogFooter>
          <Button variant="outline" @click="cancelDelete">
            {{ t('secretary.studentRegistry.editStudentDialog.deleteConfirm.cancel') }}
          </Button>
          <Button
            variant="destructive"
            :loading="deleteStudentMutation.isPending.value"
            @click="onDeleteConfirm"
          >
            {{ t('secretary.studentRegistry.editStudentDialog.deleteConfirm.confirm') }}
          </Button>
        </DialogFooter>
      </template>
    </DialogContent>
  </Dialog>
</template>
