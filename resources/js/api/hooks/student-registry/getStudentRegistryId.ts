import { StudentRegistryService } from '../../services/student-registry';
import { ApiError } from '@/api/error';
import { useQuery } from '@tanstack/vue-query';
import { type MaybeRef, computed, toValue } from 'vue';

export const useGetStudentRegistryId = (unitId: MaybeRef<number | undefined>) =>
  useQuery({
    queryKey: computed(() => ['getStudentRegistryId', toValue(unitId)]),
    queryFn: async () => {
      try {
        return await StudentRegistryService.getIdBySchoolUnit(toValue(unitId)!);
      } catch (error) {
        if (error instanceof ApiError && error.code === 'STUDENT_REGISTRY_NOT_CREATED') return null;
        throw error;
      }
    },
    enabled: computed(() => !!toValue(unitId)),
  });
