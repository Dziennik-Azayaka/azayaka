<script setup lang="ts">
import { useGetClassById } from '@/api/hooks/classes/getClassById';
import PanelPageHeader from '@/components/panel-layout/PanelPageHeader.vue';
import { EmptyLoading, EmptyLoadingError } from '@/components/ui/empty';
import { schoolYearString } from '@/lib/utils';
import ClassFormTutorTable from '@/modules/administrator/components/classes/ClassFormTutorTable.vue';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
import { useRoute } from 'vue-router';

const { t } = useI18n();
const route = useRoute();

const classId = computed(() => Number(route.params.classId));

const { data: class_, isError, isFetching, refetch } = useGetClassById(classId);

const title = computed(() => {
  if (!class_.value) return t('administrator.classes.details');
  if (class_.value.level)
    return `${class_.value.level}${class_.value.mark} (${class_.value.schoolUnit.shortName})`;
  return `${schoolYearString(class_.value.startingClassificationPeriodYear)} ${class_.value.mark} (${class_.value.schoolUnit.shortName})`;
});
</script>

<template>
  <PanelPageHeader :title="title" />
  <EmptyLoading v-if="isFetching" />
  <EmptyLoadingError v-if="isError" @refresh="refetch" />

  <template v-if="class_">
    <section class="border rounded-lg overflow-hidden">
      <div
        class="px-3 sm:px-5 py-4 flex not-sm:flex-col justify-between sm:items-center gap-2 bg-accent"
      >
        <h2 class="font-semibold">{{ t('common.generalInfo') }}</h2>
      </div>
      <dl>
        <div class="grid sm:grid-cols-[1fr_4fr] gap-x-4 sm:px-5 p-3 border-t">
          <dt class="text-sm/6 font-medium">
            {{ t('common.data.schoolUnit') }}
          </dt>
          <dd class="text-sm/6 text-foreground/80">
            {{ class_.schoolUnit.name }} ({{ class_.schoolUnit.shortName }})
          </dd>
        </div>
        <div class="grid sm:grid-cols-[1fr_4fr] gap-x-4 sm:px-5 p-3 border-t">
          <dt class="text-sm/6 font-medium">
            {{ t('administrator.classes.data.mark') }}
          </dt>
          <dd class="text-sm/6 text-foreground/80">{{ class_.mark }}</dd>
        </div>
        <div class="grid sm:grid-cols-[1fr_4fr] gap-x-4 sm:px-5 p-3 border-t">
          <dt class="text-sm/6 font-medium">
            {{ t('administrator.classes.data.alias') }}
          </dt>
          <dd class="text-sm/6 text-foreground/80">{{ class_.alias }}</dd>
        </div>
        <div class="grid sm:grid-cols-[1fr_4fr] gap-x-4 sm:px-5 p-3 border-t">
          <dt class="text-sm/6 font-medium">
            {{ t('administrator.classes.data.startingSchoolYear') }}
          </dt>
          <dd class="text-sm/6 text-foreground/80">
            {{ schoolYearString(class_.startingClassificationPeriodYear) }}
            <template v-if="class_.promoteEvery === 'semester'">
              ({{ class_.startingClassificationPeriodNumber }}. {{ t('common.data.period') }})
            </template>
          </dd>
        </div>
        <div class="grid sm:grid-cols-[1fr_4fr] gap-x-4 sm:px-5 p-3 border-t">
          <dt class="text-sm/6 font-medium">
            {{ t('administrator.classes.data.promoteEvery.label') }}
          </dt>
          <dd class="text-sm/6 text-foreground/80">
            {{ t(`administrator.classes.data.promoteEvery.${class_.promoteEvery}`) }}
          </dd>
        </div>
        <div class="grid sm:grid-cols-[1fr_4fr] gap-x-4 sm:px-5 p-3 border-t" v-if="class_.level">
          <dt class="text-sm/6 font-medium">{{ t('administrator.classes.data.level') }}</dt>
          <dd class="text-sm/6 text-foreground/80">{{ class_.level }}</dd>
        </div>
        <div class="grid sm:grid-cols-[1fr_4fr] gap-x-4 sm:px-5 p-3 border-t">
          <dt class="text-sm/6 font-medium">
            {{ t('administrator.classes.data.teachingCycleLength') }}
          </dt>
          <dd class="text-sm/6 text-foreground/80">
            {{ class_.teachingCycleLength }}
            {{ t('common.pluralization.year', class_.teachingCycleLength) }}
          </dd>
        </div>
      </dl>
    </section>

    <ClassFormTutorTable :form-tutors="class_.formTutors" class="mt-6" />
  </template>
</template>
