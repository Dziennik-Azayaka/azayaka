import { SchoolStructureService } from '../../services/school-structure';
import type { SchoolUnitBodyDTO } from '@/api/dtos/school-unit';
import { useMutation, useQueryClient } from '@tanstack/vue-query';

export const useCreateSchoolUnit = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationKey: ['createSchoolUnit'],
    mutationFn: (body: SchoolUnitBodyDTO) => SchoolStructureService.createSchoolUnit(body),
    onSuccess: async () => {
      await queryClient.invalidateQueries({ queryKey: ['getSchoolStructure'] });
      await queryClient.invalidateQueries({ queryKey: ['getSchoolUnits'] });
    },
  });
};
