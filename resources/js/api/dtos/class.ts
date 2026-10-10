import type { Class, FormTutor } from '../types/class';

export interface ClassDTO {
  id: number;
  schoolUnit: {
    id: number;
    name: string;
    shortName: string;
  };
  promoteEvery: 'year' | 'semester';
  alias: string;
  mark: string;
  startingClassificationPeriodId: number;
  startingClassificationPeriodYear: number;
  startingClassificationPeriodNumber: number;
  teachingCycleLength: number;
  level: number;
  formTutors: FormTutorDTO[];
}

export const classFromDTO = (dto: ClassDTO): Class => ({
  ...dto,
  formTutors: dto.formTutors.map(formTutorFromDTO),
});

export interface FormTutorDTO {
  employeeId: number;
  firstName: string;
  lastName: string;
  dateFrom: string;
  dateTo: string;
}

export const formTutorFromDTO = (dto: FormTutorDTO): FormTutor => ({
  ...dto,
  dateFrom: new Date(dto.dateFrom),
  dateTo: new Date(dto.dateTo),
});

export type GetClassFilter = 'archive' | 'current' | 'future';

export interface ClassBodyDTO {
  alias: string | null;
  mark: string;
  startingClassificationPeriodId: number;
  teachingCycleLength: number;
  promoteEvery: string;
  employees: { id: number }[];
  schoolUnitId: number;
}
