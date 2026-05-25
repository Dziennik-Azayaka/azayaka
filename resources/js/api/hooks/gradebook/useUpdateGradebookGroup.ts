import { GradebookService } from '@/api/services/gradebook';
import { useMutation, useQueryClient } from '@tanstack/vue-query';
import { type MaybeRef, toValue } from 'vue';

export const useUpdateGradebookGroup = (gradebookId: MaybeRef<number | undefined>) => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: ({
      groupId,
      name,
      shortcut,
      studentIds,
    }: {
      groupId: number;
      name: string;
      shortcut: string;
      studentIds: number[];
    }) => GradebookService.updateGroup(groupId, name, shortcut, studentIds),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['getGradebookGroups', toValue(gradebookId)] });
    },
  });
};
