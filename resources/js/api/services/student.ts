import { studentFromDTO, type StudentDTO } from '../dtos/student';
import type { Student } from '../types/student';
import { http } from '@/config/ofetch';

export type StudentStatusFilter = 'active' | 'inactive' | 'trashesd';

export const StudentService = {
  getFiltered: (
    registryId: number,
    birthYear: number | null,
    gender: string | null,
    status: StudentStatusFilter | null,
  ): Promise<Student[]> =>
    http<StudentDTO[]>(`/studentRegistry/${registryId}`, {
      method: 'GET',
      query: { birthYear, gender, status },
    }).then((res) => res.map(studentFromDTO)),
};
