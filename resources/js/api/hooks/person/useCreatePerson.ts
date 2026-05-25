import { PersonService } from '../../services/person';
import type { CreatePersonBodyDTO } from '@/api/dtos/person';
import { useMutation, useQueryClient } from '@tanstack/vue-query';

export const useCreatePerson = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationKey: ['createPerson'],
    mutationFn: ({
      schoolUnitId,
      data,
    }: {
      schoolUnitId: number;
      data: CreatePersonBodyDTO;
    }) => PersonService.create(schoolUnitId, data),
    onSuccess: async () => {
      await queryClient.invalidateQueries({ queryKey: ['getFilteredStudents'] });
    },
  });
};
