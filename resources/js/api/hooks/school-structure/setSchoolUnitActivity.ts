import { SchoolStructureService } from '../../services/school-structure';
import { useMutation, useQueryClient } from '@tanstack/vue-query';

export const useSetSchoolUnitActivity = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationKey: ['setSchoolUnitActivity'],
    mutationFn: ({ id, state, password }: { id: number; state: boolean; password: string }) =>
      SchoolStructureService.setSchoolUnitActivity(id, state, password),
    onSuccess: async () => {
      await queryClient.invalidateQueries({ queryKey: ['getSchoolStructure'] });
      await queryClient.invalidateQueries({ queryKey: ['getSchoolUnits'] });
    },
  });
};
