import { SubjectService } from '../../services/subject';
import type { SubjectBodyDTO } from '@/api/dtos/subject';
import { useMutation, useQueryClient } from '@tanstack/vue-query';

export const useAddSubject = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationKey: ['addSubject'],
    mutationFn: (body: SubjectBodyDTO) => SubjectService.add(body),
    onSuccess: async () => {
      await queryClient.invalidateQueries({ queryKey: ['getSubjects'] });
    },
  });
};
