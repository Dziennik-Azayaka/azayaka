<script setup lang="ts">
import { useGetClassificationPeriodsByUnit } from '@/api/hooks/classification-period/getClassificationPeriodsByUnit';
import type { ClassificationPeriod } from '@/api/types/classification-period';
import { Button } from '@/components/ui/button';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { currentSchoolYear, schoolYearString } from '@/lib/utils';
import { LucideChevronDown, LucideChevronLeft, LucideChevronRight } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps<{ unitId: number }>();
const model = defineModel<{ schoolYear: number; number: number; id: number }>();

const unitId = computed(() => props.unitId);

const { t } = useI18n();

const schoolYear = ref(currentSchoolYear());

function nextYear() {
  if (schoolYear.value >= 2100) return;
  schoolYear.value += 1;
}

function previousYear() {
  if (schoolYear.value <= 2000) return;
  schoolYear.value -= 1;
}

function setPeriod(period: ClassificationPeriod) {
  model.value = period;
}

const { data, isFetching, isError } = useGetClassificationPeriodsByUnit(unitId, schoolYear);
</script>

<template>
  <Popover>
    <PopoverTrigger class="w-full" as-child>
      <button
        class="select-none border-input data-placeholder:text-muted-foreground [&_svg:not([class*='text-'])]:text-muted-foreground focus-visible:border-ring focus-visible:ring-ring/50 aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive dark:bg-input/30 dark:hover:bg-input/50 flex w-fit items-center justify-between gap-2 rounded-md border bg-transparent px-3 py-2 text-sm whitespace-nowrap shadow-xs transition-[color,box-shadow] outline-none focus-visible:ring-[3px] disabled:cursor-not-allowed disabled:opacity-50 data-[size=default]:h-9 data-[size=sm]:h-8 *:data-[slot=select-value]:line-clamp-1 *:data-[slot=select-value]:flex *:data-[slot=select-value]:items-center *:data-[slot=select-value]:gap-2 [&_svg]:pointer-events-none [&_svg]:shrink-0 [&_svg:not([class*='size-'])]:size-4 overflow-hidden"
        :disabled="unitId === undefined"
        :title="unitId === undefined ? 'Musisz wcześniej wybrać jednostkę' : undefined"
      >
        <span class="text-muted-foreground" v-if="!model?.schoolYear">{{ t('common.actions.select') }}</span>
        <span v-else>
          {{ schoolYearString(model.schoolYear) }}: {{ t('common.data.period') }} {{ model.number }}.
        </span>
        <LucideChevronDown aria-hidden class="text-muted-foreground size-4 opacity-50" />
      </button>
    </PopoverTrigger>
    <PopoverContent>
      <div class="flex justify-between items-center mb-2">
        <span class="font-semibold">{{ schoolYearString(schoolYear) }}</span>
        <span>
          <Button size="icon" variant="ghost" @click="previousYear">
            <LucideChevronLeft :aria-label="t('common.actions.previousYear')" />
          </Button>
          <Button size="icon" variant="ghost" @click="nextYear">
            <LucideChevronRight :aria-label="t('common.actions.nextYear')" />
          </Button>
        </span>
      </div>
      <ul v-if="data && data.length">
        <li v-for="period in data" :key="period.id" class="text-sm">
          <button
            @click="() => setPeriod(period)"
            class="px-2 py-1.5 rounded-md hover:bg-accent transition-all w-full text-left cursor-pointer"
          >
            {{ t('common.data.period') }} {{ period.number }}.
          </button>
        </li>
      </ul>
      <p class="text-center text-muted-foreground text-sm" v-else-if="isFetching">
        {{ t('common.pleaseWait') }}
      </p>
      <p class="text-center text-muted-foreground text-sm" v-else-if="isError">
        {{ t('common.loadingDataError') }}
      </p>
      <p class="text-center text-muted-foreground text-sm" v-else>
        {{ t('administrator.classes.periodSelect.noPeriods') }}
      </p>
    </PopoverContent>
  </Popover>
</template>
