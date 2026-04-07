import { SchoolStructureService } from '../../services/school-structure';
import { useMutation, useQueryClient } from '@tanstack/vue-query';

export const useCreateSchoolComplex = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationKey: ['createSchoolComplex'],
    mutationFn: (name: string) => SchoolStructureService.createSchoolComplex(name),
    onSuccess: async () => {
      await queryClient.invalidateQueries({ queryKey: ['getSchoolStructure'] });
      await queryClient.invalidateQueries({ queryKey: ['getSchoolUnits'] });
    },
  });
};
