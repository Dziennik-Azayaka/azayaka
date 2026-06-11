import { StudentService, type StudentStatusFilter } from '../services/student';
import { useQuery } from '@tanstack/vue-query';
import { unref, type MaybeRef } from 'vue';

export const useGetFilteredStudents = (
  registryId: number,
  birthYear: MaybeRef<number | null>,
  gender: MaybeRef<string | null>,
  status: MaybeRef<StudentStatusFilter | null>,
) =>
  useQuery({
    queryKey: ['getFilteredStudents'],
    queryFn: () =>
      StudentService.getFiltered(registryId, unref(birthYear), unref(gender), unref(status)),
  });
