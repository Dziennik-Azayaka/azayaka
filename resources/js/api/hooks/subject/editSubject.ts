import { SubjectService } from '../../services/subject';
import type { SubjectBodyDTO } from '@/api/dtos/subject';
import { useMutation, useQueryClient } from '@tanstack/vue-query';

export const useEditSubject = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationKey: ['editSubject'],
    mutationFn: ({ id, body }: { id: number; body: SubjectBodyDTO }) =>
      SubjectService.edit(id, body),
    onSuccess: async () => {
      await queryClient.invalidateQueries({ queryKey: ['getSubjects'] });
    },
  });
};
