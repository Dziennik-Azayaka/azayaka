import { SchoolStructureService } from '../../services/school-structure';
import { useMutation, useQueryClient } from '@tanstack/vue-query';

export const useEditSchoolComplex = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationKey: ['editSchoolComplex'],
    mutationFn: ({ id, name }: { id: number; name: string }) =>
      SchoolStructureService.editSchoolComplex(id, name),
    onSuccess: async () => {
      await queryClient.invalidateQueries({ queryKey: ['getSchoolStructure'] });
    },
  });
};
