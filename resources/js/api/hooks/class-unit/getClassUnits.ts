import { ClassUnitService } from '@/api/services/class-unit';
import { useQuery } from '@tanstack/vue-query';

export const useGetClassUnits = () =>
  useQuery({
    queryKey: ['getClassUnits'],
    queryFn: () => ClassUnitService.list(),
  });
