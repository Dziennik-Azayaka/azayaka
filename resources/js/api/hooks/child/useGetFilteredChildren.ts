import { ChildService } from '../../services/child';
import { useQuery } from '@tanstack/vue-query';
import { unref, type MaybeRef, type Ref } from 'vue';

export const useGetFilteredChildren = (
  registryId: number,
  page: Ref<number>,
  birthYear: MaybeRef<string | undefined>,
  gender: MaybeRef<string | null>,
  sort: MaybeRef<string | null>,
  order: MaybeRef<string | null>,
) =>
  useQuery({
    queryKey: ['getFilteredChildren', registryId, page, birthYear, gender, sort, order],
    queryFn: () =>
      ChildService.getFiltered(
        registryId,
        unref(page),
        unref(birthYear),
        unref(gender),
        unref(sort),
        unref(order),
      ),
  });
