<script setup lang="ts">
import { useCreatePerson } from '@/api/hooks/person/useCreatePerson';
import { useLookupPerson } from '@/api/hooks/person/useLookupPerson';
import { useEnrollChild } from '@/api/hooks/child/useEnrollChild';
import { useEnrollStudent } from '@/api/hooks/student/useEnrollStudent';
import { useGetStudentRegistryId } from '@/api/hooks/student-registry/getStudentRegistryId';
import { ErrorBanner } from '@/components/ui/banner';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog';
import { EmptyLoading } from '@/components/ui/empty';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Separator } from '@/components/ui/separator';

import { computed, ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import NewPersonForm from '../student-registry/NewPersonForm.vue';
import type { PersonFormValues } from '../../forms/person';
import type { CreatePersonBodyDTO } from '@/api/dtos/person';
import type { LookupFoundPerson } from '@/api/services/person';

const props = defineProps<{
  registryId: number;
  schoolUnitId: number;
}>();

const { t } = useI18n();

const open = defineModel<boolean>('open', { default: false });

type Step = 'identity' | 'lookup' | 'found' | 'notFound';
const currentStep = ref<Step>('identity');
const identityType = ref<'pesel' | 'alternate'>('pesel');
const identityValue = ref('');
const lookupData = ref<LookupFoundPerson | null>(null);
const addToStudentRegistry = ref(false);
const admissionDate = ref('');

const { data: studentRegistryId } = useGetStudentRegistryId(computed(() => props.schoolUnitId));
const showStudentCheckbox = computed(() => studentRegistryId.value != null);

const lookupMutation = useLookupPerson();
const createPersonMutation = useCreatePerson();
const enrollChildMutation = useEnrollChild();
const enrollStudentMutation = useEnrollStudent();

const lookupError = computed(() => lookupMutation.error.value);
const submitError = computed(
  () =>
    createPersonMutation.error.value ??
    enrollChildMutation.error.value ??
    enrollStudentMutation.error.value,
);

const isSubmitting = computed(
  () =>
    createPersonMutation.isPending.value ||
    enrollChildMutation.isPending.value ||
    enrollStudentMutation.isPending.value,
);

function toggleIdentityType() {
  identityType.value = identityType.value === 'pesel' ? 'alternate' : 'pesel';
  identityValue.value = '';
}

const canProceed = computed(() => identityValue.value.trim().length > 0);

function onNext() {
  if (!canProceed.value) return;
  currentStep.value = 'lookup';
  lookupMutation.mutate(
    {
      schoolUnitId: props.schoolUnitId,
      data:
        identityType.value === 'pesel'
          ? { pesel: identityValue.value }
          : { alternateIdentityDocument: identityValue.value },
    },
    {
      onSuccess: (result) => {
        if (result.found) {
          lookupData.value = result;
          currentStep.value = 'found';
        } else {
          currentStep.value = 'notFound';
        }
      },
    },
  );
}

async function onConfirmExisting() {
  if (!lookupData.value) return;

  const mutations: Promise<unknown>[] = [
    enrollChildMutation.mutateAsync({
      childrenRegistryId: props.registryId,
      personId: lookupData.value.id,
    }),
  ];

  if (addToStudentRegistry.value && studentRegistryId.value && admissionDate.value) {
    mutations.push(
      enrollStudentMutation.mutateAsync({
        registryId: studentRegistryId.value,
        personId: lookupData.value.id,
        admissionDate: admissionDate.value,
      }),
    );
  }

  try {
    await Promise.all(mutations);
    open.value = false;
  } catch {
    // error shown via submitError
  }
}

async function onFormSubmit(values: PersonFormValues) {
  const body: CreatePersonBodyDTO = {
    ...values,
    childrenRegistryId: props.registryId,
  };

  if (addToStudentRegistry.value && studentRegistryId.value) {
    body.studentRegistryId = studentRegistryId.value;
    body.admissionDate = values.admissionDate;
  }

  createPersonMutation.mutate(
    { schoolUnitId: props.schoolUnitId, data: body },
    {
      onSuccess: () => {
        open.value = false;
      },
    },
  );
}

function goBack() {
  currentStep.value = 'identity';
  lookupData.value = null;
  addToStudentRegistry.value = false;
  admissionDate.value = '';
  lookupMutation.reset();
  createPersonMutation.reset();
  enrollChildMutation.reset();
  enrollStudentMutation.reset();
}

function resetDialog() {
  currentStep.value = 'identity';
  identityType.value = 'pesel';
  identityValue.value = '';
  lookupData.value = null;
  addToStudentRegistry.value = false;
  admissionDate.value = '';
  lookupMutation.reset();
  createPersonMutation.reset();
  enrollChildMutation.reset();
  enrollStudentMutation.reset();
}

watch(open, (val) => {
  if (val) resetDialog();
});

const step3bInitialValues = computed<Partial<PersonFormValues>>(() => ({
  [identityType.value === 'pesel' ? 'pesel' : 'alternateIdentityDocument']: identityValue.value,
}));

const stepTitle = computed(() => {
  switch (currentStep.value) {
    case 'found':
      return t('secretary.childrenRegistry.addChildDialog.step3a.title');
    case 'notFound':
      return t('secretary.childrenRegistry.addChildDialog.step3b.title');
    default:
      return t('secretary.childrenRegistry.addChildDialog.title');
  }
});

const stepDescription = computed(() => {
  switch (currentStep.value) {
    case 'found':
      return t('secretary.childrenRegistry.addChildDialog.step3a.description');
    case 'notFound':
      return t('secretary.childrenRegistry.addChildDialog.step3b.description');
    default:
      return t('secretary.childrenRegistry.addChildDialog.step1.description');
  }
});
</script>

<template>
  <Dialog v-model:open="open">
    <DialogContent class="sm:max-w-2xl">
      <DialogHeader>
        <DialogTitle>{{ stepTitle }}</DialogTitle>
        <DialogDescription>{{ stepDescription }}</DialogDescription>
      </DialogHeader>

      <!-- Step 1: Identity -->
      <template v-if="currentStep === 'identity'">
        <div class="space-y-3">
          <Label for="identity-input">{{
            identityType === 'pesel'
              ? t('secretary.childrenRegistry.addChildDialog.step1.pesel')
              : t('secretary.childrenRegistry.addChildDialog.step1.alternateDocument')
          }}</Label>
          <Input
            id="identity-input"
            v-model="identityValue"
            :placeholder="
              identityType === 'pesel'
                ? t('secretary.childrenRegistry.addChildDialog.step1.pesel')
                : t('secretary.childrenRegistry.addChildDialog.step1.alternateDocument')
            "
            @keyup.enter="onNext"
          />
        </div>
        <DialogFooter class="sm:justify-between">
          <Button variant="ghost" type="button" @click="toggleIdentityType">
            {{
              identityType === 'pesel'
                ? t('secretary.childrenRegistry.addChildDialog.step1.noPesel')
                : t('secretary.childrenRegistry.addChildDialog.step1.hasPesel')
            }}
          </Button>
          <Button :disabled="!canProceed" @click="onNext">
            {{ t('secretary.childrenRegistry.addChildDialog.next') }}
          </Button>
        </DialogFooter>
      </template>

      <!-- Step 2: Lookup -->
      <template v-else-if="currentStep === 'lookup'">
        <EmptyLoading v-if="lookupMutation.isPending.value" />
        <div v-else-if="lookupError" class="space-y-3">
          <ErrorBanner :error="lookupError" />
          <div class="flex gap-3 justify-end">
            <Button variant="outline" @click="goBack">
              {{ t('secretary.childrenRegistry.addChildDialog.back') }}
            </Button>
            <Button @click="onNext">{{ t('common.actions.retry') }}</Button>
          </div>
        </div>
        <p v-else class="text-muted-foreground text-sm text-center py-4">
          {{ t('secretary.childrenRegistry.addChildDialog.step2.lookingUp') }}
        </p>
      </template>

      <!-- Step 3a: Person found -->
      <template v-else-if="currentStep === 'found'">
        <div class="space-y-3">
          <div class="grid sm:grid-cols-2 gap-3">
            <div>
              <Label for="found-lastname">{{ t('common.data.lastName') }}</Label>
              <Input id="found-lastname" :model-value="lookupData?.lastName" disabled />
            </div>
            <div>
              <Label for="found-firstname">{{ t('common.data.firstName') }}</Label>
              <Input id="found-firstname" :model-value="lookupData?.firstName" disabled />
            </div>
            <div v-if="lookupData?.secondName">
              <Label for="found-secondname">{{ t('common.data.secondName') }}</Label>
              <Input id="found-secondname" :model-value="lookupData?.secondName" disabled />
            </div>
            <div>
              <Label for="found-birthdate">{{ t('common.data.birthDate') }}</Label>
              <Input id="found-birthdate" :model-value="lookupData?.birthdate" disabled />
            </div>
          </div>
          <Separator />
          <template v-if="showStudentCheckbox">
            <div class="flex items-center gap-2">
              <Checkbox id="student-check" v-model:checked="addToStudentRegistry" />
              <Label for="student-check" class="font-normal text-sm">
                {{ t('secretary.childrenRegistry.addChildDialog.addToStudentRegistry') }}
              </Label>
            </div>
            <div v-if="addToStudentRegistry">
              <Label for="admission-date">{{ t('secretary.studentRegistry.addStudentDialog.step3a.admissionDate') }}</Label>
              <Input id="admission-date" v-model="admissionDate" type="date" class="max-w-xs" />
            </div>
          </template>
          <ErrorBanner v-if="submitError" :error="submitError" />
        </div>
        <DialogFooter>
          <Button variant="outline" @click="goBack">
            {{ t('secretary.childrenRegistry.addChildDialog.back') }}
          </Button>
          <Button
            :loading="isSubmitting"
            :disabled="addToStudentRegistry && !admissionDate"
            @click="onConfirmExisting"
          >
            {{ t('secretary.childrenRegistry.addChildDialog.confirm') }}
          </Button>
        </DialogFooter>
      </template>

      <!-- Step 3b: New person form -->
      <template v-else-if="currentStep === 'notFound'">
        <NewPersonForm
          :is-pending="isSubmitting"
          :initial-values="step3bInitialValues"
          :identity-readonly="true"
          :error="submitError"
          @submit="onFormSubmit"
        >
          <template v-if="showStudentCheckbox">
            <div class="flex items-center gap-2 my-3">
              <Checkbox id="student-check-form" v-model:checked="addToStudentRegistry" />
              <Label for="student-check-form" class="font-normal text-sm">
                {{ t('secretary.childrenRegistry.addChildDialog.addToStudentRegistry') }}
              </Label>
            </div>
          </template>
          <DialogFooter>
            <Button variant="outline" type="button" @click="goBack">
              {{ t('secretary.childrenRegistry.addChildDialog.back') }}
            </Button>
            <Button :loading="isSubmitting" type="submit">
              {{ t('common.actions.save') }}
            </Button>
          </DialogFooter>
        </NewPersonForm>
      </template>
    </DialogContent>
  </Dialog>
</template>
