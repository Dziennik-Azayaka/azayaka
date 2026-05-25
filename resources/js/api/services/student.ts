import type { PaginatedResourceDTO } from '../dtos/paginated-resource';
import { studentFromDTO, type StudentDTO } from '../dtos/student';
import type { PaginatedResource } from '../types/paginated-resource';
import type { Student } from '../types/student';
import { http } from '@/config/ofetch';

export type StudentStatusFilter = 'active' | 'inactive' | 'trashed';

export const StudentService = {
  getFiltered: (
    registryId: number,
    page: number,
    birthYear: string | undefined,
    gender: string | null,
    status: StudentStatusFilter | null,
    classUnitId: number | null = null,
    sort: string | null = null,
    order: string | null = null,
  ): Promise<PaginatedResource<Student>> =>
    http<PaginatedResourceDTO<StudentDTO>>(`/studentRegistry/${registryId}`, {
      method: 'GET',
      query: { birthYear, gender, status, page, classUnitId, sort, order },
    }).then((res) => ({ ...res, data: res.data.map(studentFromDTO) })),
};
