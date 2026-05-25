import { PersonService } from '../../services/person';
import type { CreatePersonBodyDTO } from '@/api/dtos/person';
import { useMutation, useQueryClient } from '@tanstack/vue-query';

export const useUpdatePerson = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationKey: ['updatePerson'],
    mutationFn: ({
      schoolUnitId,
      personId,
      data,
    }: {
      schoolUnitId: number;
      personId: number;
      data: CreatePersonBodyDTO;
    }) => PersonService.update(schoolUnitId, personId, data),
    onSuccess: async () => {
      await queryClient.invalidateQueries({ queryKey: ['getFilteredStudents'] });
    },
  });
};
