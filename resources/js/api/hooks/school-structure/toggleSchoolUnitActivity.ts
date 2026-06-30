import { SchoolStructureService } from '../../services/school-structure';
import { useMutation, useQueryClient } from '@tanstack/vue-query';

export const useToggleSchoolUnitActivity = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationKey: ['setSchoolUnitActivity'],
    mutationFn: ({ id, password }: { id: number; password: string }) =>
      SchoolStructureService.toggleSchoolUnitActivity(id, password),
    onSuccess: async () => {
      await queryClient.invalidateQueries({ queryKey: ['getSchoolStructure'] });
      await queryClient.invalidateQueries({ queryKey: ['getSchoolUnits'] });
    },
  });
};
