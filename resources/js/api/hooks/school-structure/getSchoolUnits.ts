import { SchoolStructureService } from '../../services/school-structure';
import { useQuery } from '@tanstack/vue-query';

export const useGetSchoolUnits = () =>
  useQuery({
    queryKey: ['getSchoolUnits'],
    queryFn: () => SchoolStructureService.getSchoolUnits(),
  });
