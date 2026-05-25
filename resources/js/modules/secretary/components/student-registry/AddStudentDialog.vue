<script setup lang="ts">
import { useCreatePerson } from '@/api/hooks/person/useCreatePerson';
import { useLookupPerson } from '@/api/hooks/person/useLookupPerson';
import { useEnrollChild } from '@/api/hooks/child/useEnrollChild';
import { useEnrollStudent } from '@/api/hooks/student/useEnrollStudent';
import { useGetChildrenRegistryId } from '@/api/hooks/children-registry/getChildrenRegistryId';
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
import NewPersonForm from './NewPersonForm.vue';
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
const admissionDate = ref('');
const addToChildrenRegistry = ref(false);

const { data: childrenRegistryId } = useGetChildrenRegistryId(computed(() => props.schoolUnitId));
const showChildrenCheckbox = computed(() => childrenRegistryId.value != null);

const lookupMutation = useLookupPerson();
const createPersonMutation = useCreatePerson();
const enrollStudentMutation = useEnrollStudent();
const enrollChildMutation = useEnrollChild();

const lookupError = computed(() => lookupMutation.error.value);
const submitError = computed(
  () => createPersonMutation.error.value ?? enrollStudentMutation.error.value ?? enrollChildMutation.error.value,
);

const isSubmitting = computed(
  () =>
    createPersonMutation.isPending.value ||
    enrollStudentMutation.isPending.value ||
    enrollChildMutation.isPending.value,
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
  if (!admissionDate.value || !lookupData.value) return;

  const mutations: Promise<unknown>[] = [
    enrollStudentMutation.mutateAsync({
      registryId: props.registryId,
      personId: lookupData.value.id,
      admissionDate: admissionDate.value,
    }),
  ];

  if (addToChildrenRegistry.value && childrenRegistryId.value) {
    mutations.push(
      enrollChildMutation.mutateAsync({
        childrenRegistryId: childrenRegistryId.value,
        personId: lookupData.value.id,
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
    studentRegistryId: props.registryId,
  };

  if (addToChildrenRegistry.value && childrenRegistryId.value) {
    body.childrenRegistryId = childrenRegistryId.value;
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
  admissionDate.value = '';
  addToChildrenRegistry.value = false;
  lookupMutation.reset();
  createPersonMutation.reset();
  enrollStudentMutation.reset();
  enrollChildMutation.reset();
}

function resetDialog() {
  currentStep.value = 'identity';
  identityType.value = 'pesel';
  identityValue.value = '';
  lookupData.value = null;
  admissionDate.value = '';
  addToChildrenRegistry.value = false;
  lookupMutation.reset();
  createPersonMutation.reset();
  enrollStudentMutation.reset();
  enrollChildMutation.reset();
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
      return t('secretary.studentRegistry.addStudentDialog.step3a.title');
    case 'notFound':
      return t('secretary.studentRegistry.addStudentDialog.step3b.title');
    default:
      return t('secretary.studentRegistry.addStudentDialog.title');
  }
});

const stepDescription = computed(() => {
  switch (currentStep.value) {
    case 'found':
      return t('secretary.studentRegistry.addStudentDialog.step3a.description');
    case 'notFound':
      return t('secretary.studentRegistry.addStudentDialog.step3b.description');
    default:
      return t('secretary.studentRegistry.addStudentDialog.step1.description');
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
              ? t('secretary.studentRegistry.addStudentDialog.step1.pesel')
              : t('secretary.studentRegistry.addStudentDialog.step1.alternateDocument')
          }}</Label>
          <Input
            id="identity-input"
            v-model="identityValue"
            :placeholder="
              identityType === 'pesel'
                ? t('secretary.studentRegistry.addStudentDialog.step1.pesel')
                : t('secretary.studentRegistry.addStudentDialog.step1.alternateDocument')
            "
            @keyup.enter="onNext"
          />
        </div>
        <DialogFooter class="sm:justify-between">
          <Button variant="ghost" type="button" @click="toggleIdentityType">
            {{
              identityType === 'pesel'
                ? t('secretary.studentRegistry.addStudentDialog.step1.noPesel')
                : t('secretary.studentRegistry.addStudentDialog.step1.hasPesel')
            }}
          </Button>
          <Button :disabled="!canProceed" @click="onNext">
            {{ t('secretary.studentRegistry.addStudentDialog.next') }}
          </Button>
        </DialogFooter>
      </template>

      <!-- Step 2: Lookup (loading/error) -->
      <template v-else-if="currentStep === 'lookup'">
        <EmptyLoading v-if="lookupMutation.isPending.value" />
        <div v-else-if="lookupError" class="space-y-3">
          <ErrorBanner :error="lookupError" />
          <div class="flex gap-3 justify-end">
            <Button variant="outline" @click="goBack">
              {{ t('secretary.studentRegistry.addStudentDialog.back') }}
            </Button>
            <Button @click="onNext">{{ t('common.actions.retry') }}</Button>
          </div>
        </div>
        <p v-else class="text-muted-foreground text-sm text-center py-4">
          {{ t('secretary.studentRegistry.addStudentDialog.step2.lookingUp') }}
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
          <div>
            <Label for="admission-date">{{ t('secretary.studentRegistry.addStudentDialog.step3a.admissionDate') }}</Label>
            <Input id="admission-date" v-model="admissionDate" type="date" class="max-w-xs" />
          </div>
          <div v-if="showChildrenCheckbox" class="flex items-center gap-2">
            <Checkbox id="children-check" v-model:checked="addToChildrenRegistry" />
            <Label for="children-check" class="font-normal text-sm">
              {{ t('secretary.studentRegistry.addStudentDialog.addToChildrenRegistry') }}
            </Label>
          </div>
          <ErrorBanner v-if="submitError" :error="submitError" />
        </div>
        <DialogFooter>
          <Button variant="outline" @click="goBack">
            {{ t('secretary.studentRegistry.addStudentDialog.back') }}
          </Button>
          <Button :loading="isSubmitting" :disabled="!admissionDate" @click="onConfirmExisting">
            {{ t('secretary.studentRegistry.addStudentDialog.confirm') }}
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
          <div class="flex items-center gap-2 my-3">
            <Checkbox
              v-if="showChildrenCheckbox"
              id="children-check-form"
              v-model:checked="addToChildrenRegistry"
            />
            <Label
              v-if="showChildrenCheckbox"
              for="children-check-form"
              class="font-normal text-sm"
            >
              {{ t('secretary.studentRegistry.addStudentDialog.addToChildrenRegistry') }}
            </Label>
          </div>
          <DialogFooter>
            <Button variant="outline" type="button" @click="goBack">
              {{ t('secretary.studentRegistry.addStudentDialog.back') }}
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
