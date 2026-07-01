<script setup lang="ts">
import { useGetEmployees } from '@/api/hooks/employee/getEmployees';
import type { Employee } from '@/api/types/employee';
import { Button } from '@/components/ui/button';
import {
  Command,
  CommandInput,
  CommandList,
  CommandEmpty,
  CommandGroup,
  CommandItem,
} from '@/components/ui/command';
import { PopoverContent, Popover, PopoverTrigger } from '@/components/ui/popover';
import { cn } from '@/lib/utils';
import { LucideCheck, LucideChevronDown, LucideRefreshCcw } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { useI18n } from 'vue-i18n';

const model = defineModel<number[]>({ default: [] });

const { t } = useI18n();
const open = ref(false);

const { data: employees, isFetching, isError, refetch } = useGetEmployees();
const teacherList = computed(() =>
  employees.value?.filter((employee) => employee.roles.has('teacher') && employee.active),
);

function toggleTeacher(employee: Employee) {
  const existing = model.value.find((id) => employee.id === id);
  if (!existing) model.value = [...model.value, employee.id];
  else model.value = model.value.filter((id) => id !== employee.id);
}

function isTeacherSelected(employee: Employee) {
  return model.value.some((id) => id === employee.id);
}

function buttonText() {
  if (!model.value.length) return t('common.actions.select');
  return model.value
    .map((id) => {
      const teacher = employees.value?.find((e) => e.id === id);
      if (!teacher) return;
      return `${teacher.lastName} ${teacher.firstName} (${teacher.shortcut})`;
    })
    .filter((t) => t)
    .join(', ');
};
</script>

<template>
  <Popover v-model:open="open">
    <PopoverTrigger as-child>
      <Button
        variant="outline"
        class="w-full justify-between text-wrap! h-auto text-left font-normal hover:bg-background hover:text-foreground"
        :class="{ 'text-muted-foreground!': !model.length }"
      >
        {{ buttonText() }}
        <LucideChevronDown class="ml-2 h-4 w-4 shrink-0 opacity-50" />
      </Button>
    </PopoverTrigger>
    <PopoverContent class="w-full p-0">
      <Command>
        <CommandInput :placeholder="t('common.select.search')" />
        <CommandList>
          <div class="p-3 flex flex-col gap-2.5 text-center text-sm" v-if="isError">
            <span class="text-muted-foreground">{{ t('common.loadingDataError') }}</span>
            <Button variant="outline" size="sm" @click="refetch">
              <LucideRefreshCcw />
              {{ t('common.actions.tryAgain') }}
            </Button>
          </div>
          <div class="p-3 text-center text-sm text-muted-foreground" v-else-if="isFetching">
            {{ t('common.pleaseWait') }}
          </div>
          <template v-if="teacherList">
            <CommandEmpty>{{ t('common.select.noResults') }}</CommandEmpty>
            <CommandGroup>
              <CommandItem
                v-for="teacher in teacherList"
                :key="teacher.id"
                :value="teacher.id"
                @select="toggleTeacher(teacher)"
              >
                <LucideCheck
                  :class="
                    cn('mr-2 h-4 w-4', isTeacherSelected(teacher) ? 'opacity-100' : 'opacity-0')
                  "
                />
                {{ teacher.lastName }} {{ teacher.firstName }} ({{ teacher.shortcut }})
              </CommandItem>
            </CommandGroup>
          </template>
        </CommandList>
      </Command>
    </PopoverContent>
  </Popover>
</template>
