import { SchoolStructureService } from '../../services/school-structure';
import type { SchoolUnitBodyDTO } from '@/api/dtos/school-unit';
import { useMutation, useQueryClient } from '@tanstack/vue-query';

export const useEditSchoolUnit = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationKey: ['editSchoolUnit'],
    mutationFn: ({ id, body }: { id: number; body: SchoolUnitBodyDTO }) =>
      SchoolStructureService.editSchoolUnit(id, body),
    onSuccess: async () => {
      await queryClient.invalidateQueries({ queryKey: ['getSchoolStructure'] });
      await queryClient.invalidateQueries({ queryKey: ['getSchoolUnits'] });
    },
  });
};
