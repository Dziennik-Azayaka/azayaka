<script setup lang="ts">
import SchoolUnitArchive from './SchoolUnitArchive.vue';
import SchoolUnitEdit from './SchoolUnitEdit.vue';
import type { SchoolUnit } from '@/api/types/school-structure';
import InstitutionTypes from '@/assets/json/institution-types.json';
import StudentCategories from '@/assets/json/student-categories.json';
import Voivodeships from '@/assets/json/voivodeships.json';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
defineProps<{ unit: SchoolUnit }>();
</script>

<template>
  <li class="border rounded-md shadow-xs overflow-hidden">
    <div
      class="px-3 sm:px-5 py-4 flex not-sm:flex-col justify-between sm:items-center gap-2 bg-accent"
    >
      <h2 class="font-semibold">
        <template v-if="!unit.active">
          {{ t('administrator.schoolStructure.archivedUnit') }}:
        </template>
        <span :class="{ italic: !unit.active }">{{ unit.name }} ({{ unit.shortName }})</span>
      </h2>
      <div class="flex gap-3">
        <SchoolUnitEdit
          :unit-id="unit.id"
          :complex-id="unit.schoolComplexId"
          :initial-values="unit"
          v-if="unit.active"
        />
        <SchoolUnitArchive :state="unit.active" :unit-id="unit.id" />
      </div>
    </div>
    <dl :class="{ 'opacity-75': !unit.active }">
      <div class="grid sm:grid-cols-[1fr_4fr] gap-x-4 sm:px-5 p-3 border-t">
        <dt class="text-sm/6 font-medium">
          {{ t('administrator.schoolStructure.data.institutionType') }}
        </dt>
        <dd class="text-sm/6 text-foreground/80">
          {{ t(InstitutionTypes.find(({ id }) => id === unit.type)!.nameTranslationId) }}
        </dd>
      </div>
      <div class="grid sm:grid-cols-[1fr_4fr] gap-x-4 sm:px-5 p-3 border-t">
        <dt class="text-sm/6 font-medium">{{ t('common.data.voivodeship') }}</dt>
        <dd class="text-sm/6 text-foreground/80">
          {{ Voivodeships.find(({ id }) => id === unit.voivodeship)!.name }}
        </dd>
      </div>
      <div class="grid sm:grid-cols-[1fr_4fr] gap-x-4 sm:px-5 p-3 border-t">
        <dt class="text-sm/6 font-medium">{{ t('common.data.municipality') }}</dt>
        <dd class="text-sm/6 text-foreground/80">{{ unit.municipality }}</dd>
      </div>
      <div class="grid sm:grid-cols-[1fr_4fr] gap-x-4 sm:px-5 p-3 border-t">
        <dt class="text-sm/6 font-medium">{{ t('common.data.district') }}</dt>
        <dd class="text-sm/6 text-foreground/80">{{ unit.district || '-' }}</dd>
      </div>
      <div class="grid sm:grid-cols-[1fr_4fr] gap-x-4 sm:px-5 p-3 border-t">
        <dt class="text-sm/6 font-medium">{{ t('common.data.address') }}</dt>
        <dd class="text-sm/6 text-foreground/80">TODO</dd>
      </div>
      <div class="grid sm:grid-cols-[1fr_4fr] gap-x-4 sm:px-5 p-3 border-t">
        <dt class="text-sm/6 font-medium">
          {{ t('administrator.schoolStructure.data.studentCategory') }}
        </dt>
        <dd class="text-sm/6 text-foreground/80">
          {{
            t(
              StudentCategories.find(({ value }) => value === unit.studentCategory)!
                .nameTranslationId,
            )
          }}
        </dd>
      </div>
    </dl>
  </li>
</template>
