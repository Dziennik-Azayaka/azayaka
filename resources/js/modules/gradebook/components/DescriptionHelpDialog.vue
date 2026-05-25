<script setup lang="ts">
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
import { SUBJECT_TYPE_GROUPS } from '@/modules/gradebook/constants';
import { computed, ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';

const NON_LINGUISTIC_ITEMS = SUBJECT_TYPE_GROUPS[0]!.items;

const SCHOOL_CONTINUATION_MAP: Record<string, Record<string, string[]>> = {
  primary: {
    fromScratch: ['II.2.', 'II.2.DJ'],
    'continuedI-III': ['II.1.', 'II.1.DJ'],
    'continuedVII-VIII': ['II.2.', 'II.2.DJ'],
  },
  trade1: {
    fromScratch: ['III.BS1.0'],
    'continuedI-III': ['III.BS1.1'],
    'continuedVII-VIII': ['III.BS1.2'],
  },
  trade2: {
    fromScratch: ['III.BS2.0'],
    'continuedI-III': ['III.BS2.1'],
    'continuedVII-VIII': ['III.BS2.2'],
  },
  liceumTechnikum: {
    fromScratch: ['III.2.0.'],
    'continuedI-III': ['III.1.P', 'III.1.R', 'III.DJ'],
    'continuedVII-VIII': ['III.2.'],
  },
};

const ALL_ITEMS = SUBJECT_TYPE_GROUPS.flatMap((g) => g.items);
const ITEM_LOOKUP = new Map(ALL_ITEMS.map((i) => [i.value, i]));

const props = defineProps<{ open: boolean }>();
const emit = defineEmits<{
  'update:open': [value: boolean];
  select: [value: string];
}>();

const { t } = useI18n();

const q1Linguistic = ref<string | null>(null);
const q2SchoolType = ref<string | null>(null);
const q3Continuation = ref<string | null>(null);

const suggestions = computed(() => {
  if (q1Linguistic.value === null) return [];

  if (q1Linguistic.value === 'no') {
    return NON_LINGUISTIC_ITEMS;
  }

  if (!q2SchoolType.value || !q3Continuation.value) return [];

  const valueKeys = SCHOOL_CONTINUATION_MAP[q2SchoolType.value]?.[q3Continuation.value] ?? [];
  return valueKeys.map((k) => ITEM_LOOKUP.get(k)).filter(Boolean) as { value: string; label: string }[];
});

function selectDescription(value: string) {
  emit('select', value);
  emit('update:open', false);
}

watch(
  () => props.open,
  (open) => {
    if (open) {
      q1Linguistic.value = null;
      q2SchoolType.value = null;
      q3Continuation.value = null;
    }
  },
);
</script>

<template>
  <Dialog :open="open" @update:open="emit('update:open', $event)">
    <DialogContent class="sm:max-w-xl">
      <DialogHeader>
        <DialogTitle>{{ t('gradebook.groups.descriptionHelp.title') }}</DialogTitle>
        <DialogDescription>
          {{ t('gradebook.groups.descriptionHelp.subtitle') }}
        </DialogDescription>
      </DialogHeader>

      <div class="flex flex-col gap-5 py-2">
        <!-- Q1: Linguistic or not -->
        <div class="flex flex-col gap-2">
          <p class="text-sm font-medium">{{ t('gradebook.groups.descriptionHelp.question1') }}</p>
          <div class="flex flex-wrap gap-2">
            <Button
              type="button"
              :variant="q1Linguistic === 'yes' ? 'default' : 'secondary'"
              size="sm"
              @click="q1Linguistic = 'yes'"
            >
              {{ t('gradebook.groups.descriptionHelp.option1Linguistic') }}
            </Button>
            <Button
              type="button"
              :variant="q1Linguistic === 'no' ? 'default' : 'secondary'"
              size="sm"
              @click="q1Linguistic = 'no'"
            >
              {{ t('gradebook.groups.descriptionHelp.option1NonLinguistic') }}
            </Button>
          </div>
        </div>

        <!-- Q2: School type (only if linguistic) -->
        <div v-if="q1Linguistic === 'yes'" class="flex flex-col gap-2">
          <p class="text-sm font-medium">{{ t('gradebook.groups.descriptionHelp.question2') }}</p>
          <div class="flex flex-wrap gap-2">
            <Button
              type="button"
              :variant="q2SchoolType === 'primary' ? 'default' : 'secondary'"
              size="sm"
              @click="q2SchoolType = 'primary'"
            >
              {{ t('gradebook.groups.descriptionHelp.option2Primary') }}
            </Button>
            <Button
              type="button"
              :variant="q2SchoolType === 'trade1' ? 'default' : 'secondary'"
              size="sm"
              @click="q2SchoolType = 'trade1'"
            >
              {{ t('gradebook.groups.descriptionHelp.option2Trade1') }}
            </Button>
            <Button
              type="button"
              :variant="q2SchoolType === 'trade2' ? 'default' : 'secondary'"
              size="sm"
              @click="q2SchoolType = 'trade2'"
            >
              {{ t('gradebook.groups.descriptionHelp.option2Trade2') }}
            </Button>
            <Button
              type="button"
              :variant="q2SchoolType === 'liceumTechnikum' ? 'default' : 'secondary'"
              size="sm"
              @click="q2SchoolType = 'liceumTechnikum'"
            >
              {{ t('gradebook.groups.descriptionHelp.option2LiceumTechnikum') }}
            </Button>
          </div>
        </div>

        <!-- Q3: Continuation (only if linguistic) -->
        <div v-if="q1Linguistic === 'yes'" class="flex flex-col gap-2">
          <p class="text-sm font-medium">{{ t('gradebook.groups.descriptionHelp.question3') }}</p>
          <div class="flex flex-wrap gap-2">
            <Button
              type="button"
              :variant="q3Continuation === 'fromScratch' ? 'default' : 'secondary'"
              size="sm"
              @click="q3Continuation = 'fromScratch'"
            >
              {{ t('gradebook.groups.descriptionHelp.option3FromScratch') }}
            </Button>
            <Button
              type="button"
              :variant="q3Continuation === 'continuedI-III' ? 'default' : 'secondary'"
              size="sm"
              @click="q3Continuation = 'continuedI-III'"
            >
              {{ t('gradebook.groups.descriptionHelp.option3ContinuedI-III') }}
            </Button>
            <Button
              type="button"
              :variant="q3Continuation === 'continuedVII-VIII' ? 'default' : 'secondary'"
              size="sm"
              @click="q3Continuation = 'continuedVII-VIII'"
            >
              {{ t('gradebook.groups.descriptionHelp.option3ContinuedVII-VIII') }}
            </Button>
          </div>
        </div>

        <!-- Results -->
        <div
          v-if="suggestions.length > 0"
          class="rounded-lg border p-3"
        >
          <p class="text-sm font-medium mb-2">
            {{ t('gradebook.groups.descriptionHelp.resultsTitle') }}
          </p>
          <div class="flex flex-col gap-1.5">
            <button
              v-for="s in suggestions"
              :key="s.value"
              type="button"
              class="flex items-center gap-2 w-full text-left px-3 py-2 rounded-md text-sm hover:bg-accent hover:text-accent-foreground cursor-pointer transition-colors"
              @click="selectDescription(s.value)"
            >
              <span class="font-mono text-xs text-muted-foreground shrink-0">{{ s.value }}</span>
              <span>{{ s.label }}</span>
            </button>
          </div>
        </div>

        <!-- Prompt to continue answering -->
        <p
          v-if="q1Linguistic === 'yes' && (!q2SchoolType || !q3Continuation)"
          class="text-sm text-muted-foreground italic"
        >
          {{ t('gradebook.groups.descriptionHelp.pleaseContinue') }}
        </p>
      </div>

      <DialogFooter>
        <DialogClose as-child>
          <Button variant="outline" type="button">
            {{ t('gradebook.groups.descriptionHelp.cancel') }}
          </Button>
        </DialogClose>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>
