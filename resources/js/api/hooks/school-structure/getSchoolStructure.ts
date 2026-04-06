import { SchoolStructureService } from '../../services/school-structure';
import { useQuery } from '@tanstack/vue-query';

export const useGetSchoolStructure = () =>
  useQuery({
    queryKey: ['getSchoolStructure'],
    queryFn: () => SchoolStructureService.getStructure(),
  });
