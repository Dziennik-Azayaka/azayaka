import type { PaginatedResourceDTO } from '../dtos/paginated-resource';
import { childFromDTO, type ChildDTO } from '../dtos/child';
import type { PaginatedResource } from '../types/paginated-resource';
import type { Child } from '../types/child';
import { http } from '@/config/ofetch';

export const ChildService = {
  enroll: (
    childrenRegistryId: number,
    data: { personId: number },
  ): Promise<{ success: true; childId: number }> =>
    http(`/childrenRegistry/${childrenRegistryId}`, { method: 'POST', body: data }),

  getFiltered: (
    registryId: number,
    page: number,
    birthYear: string | undefined,
    gender: string | null,
    sort: string | null = null,
    order: string | null = null,
  ): Promise<PaginatedResource<Child>> =>
    http<PaginatedResourceDTO<ChildDTO>>(`/childrenRegistry/${registryId}`, {
      method: 'GET',
      query: { birthYear, gender, page, sort, order },
    }).then((res) => ({ ...res, data: res.data.map(childFromDTO) })),

  delete: (id: number): Promise<{ success: true }> =>
    http(`/children/${id}`, { method: 'DELETE' }),
};
