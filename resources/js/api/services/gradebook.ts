import { type GradebookDTO, gradebookFromDTO } from '../dtos/gradebook';
import type { Gradebook } from '../types/gradebook';
import type { GradebookGroup } from '../types/gradebook-group';
import type { GradebookStudent } from '../types/gradebook-student';
import { http } from '@/config/ofetch';

export const GradebookService = {
  list: (
    schoolUnitId: number,
    params?: { classUnitId?: number; schoolYear?: number },
  ): Promise<Gradebook[]> =>
    http<GradebookDTO[]>('/schoolUnits/' + schoolUnitId + '/gradebooks', {
      method: 'GET',
      query: params as Record<string, unknown>,
    }).then((res) => res.map(gradebookFromDTO)),

  create: (
    classificationPeriodId: number,
    classUnitId: number,
  ): Promise<{ success: boolean; gradebookId: number }> =>
    http('/gradebooks', {
      method: 'POST',
      body: { classificationPeriodId, classUnitId },
    }),

  listStudents: (gradebookId: number): Promise<GradebookStudent[]> =>
    http<GradebookStudent[]>(`/gradebooks/${gradebookId}/students`, {
      method: 'GET',
    }),

  attachStudents: (
    gradebookId: number,
    studentIds: number[],
    positions: number[],
  ): Promise<{ success: boolean }> =>
    http(`/gradebooks/${gradebookId}/students`, {
      method: 'POST',
      body: { studentIds, positions },
    }),

  listGroups: (gradebookId: number): Promise<GradebookGroup[]> =>
    http<GradebookGroup[]>(`/gradebooks/${gradebookId}/groups`, {
      method: 'GET',
    }),

  createGroup: (
    gradebookId: number,
    name: string,
    shortcut: string,
  ): Promise<{ success: boolean }> =>
    http(`/gradebooks/${gradebookId}/groups`, {
      method: 'POST',
      body: { name, shortcut },
    }),

  updateGroup: (
    groupId: number,
    name: string,
    shortcut: string,
    studentIds: number[],
  ): Promise<{ success: boolean }> =>
    http(`/gradebooks/groups/${groupId}`, {
      method: 'PUT',
      body: { name, shortcut, studentIds },
    }),

  addSubjectToGroup: (
    groupId: number,
    subjectId: number,
    description: string,
    teacherIds?: number[],
  ): Promise<{ success: boolean }> =>
    http(`/gradebooks/groups/${groupId}/subjects`, {
      method: 'POST',
      body: { subject_id: subjectId, description, teachers: teacherIds },
    }),

  updateGroupSubject: (
    groupId: number,
    subjectId: number,
    description: string,
  ): Promise<{ success: boolean }> =>
    http(`/gradebooks/groups/${groupId}/subjects/${subjectId}`, {
      method: 'PUT',
      body: { description },
    }),

  updateGroupSubjectTeachers: (
    groupId: number,
    subjectId: number,
    teacherIds: number[],
  ): Promise<{ success: boolean }> =>
    http(`/gradebooks/groups/${groupId}/subjects/${subjectId}/teachers`, {
      method: 'PUT',
      body: { teachers: teacherIds },
    }),

  deleteGroupSubject: (
    groupId: number,
    subjectId: number,
  ): Promise<{ success: boolean }> =>
    http(`/gradebooks/groups/${groupId}/subjects/${subjectId}`, {
      method: 'DELETE',
    }),
};
