import { SubjectService } from '../../services/subject';
import { useQuery } from '@tanstack/vue-query';

export const useGetSubjects = () =>
  useQuery({
    queryKey: ['getSubjects'],
    queryFn: () => SubjectService.getAll(),
  });
