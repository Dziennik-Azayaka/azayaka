import { StudentService, type StudentStatusFilter } from '../../services/student';
import { useQuery } from '@tanstack/vue-query';
import { unref, type MaybeRef, type Ref } from 'vue';

export const useGetFilteredStudents = (
  registryId: number,
  page: Ref<number>,
  classUnitId: MaybeRef<number | null>,
  sort: MaybeRef<string | null>,
  order: MaybeRef<string | null>,
  birthYear: MaybeRef<string | undefined>,
  gender: MaybeRef<string | null>,
  status: MaybeRef<StudentStatusFilter | null>,
) =>
  useQuery({
    queryKey: ['getFilteredStudents', registryId, page, classUnitId, sort, order, birthYear, gender, status],
    queryFn: () =>
      StudentService.getFiltered(
        registryId,
        unref(page),
        unref(birthYear),
        unref(gender),
        unref(status),
        unref(classUnitId),
        unref(sort),
        unref(order),
      ),
  });
