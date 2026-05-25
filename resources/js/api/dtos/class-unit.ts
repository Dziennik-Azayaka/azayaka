import type { ClassUnit, FormTutor } from '../types/class-unit';

export interface ClassUnitDTO {
  id: number;
  alias: string | null;
  mark: string;
  startingClassificationPeriodId: number;
  startingClassificationPeriodYear: number;
  startingClassificationPeriodNumber: number;
  teachingCycleLength: number;
  level: number | null;
  promoteEvery: 'year' | 'semester';
  formTutors: FormTutorDTO[];
  schoolUnit: {
    id: number;
    name: string;
    shortName: string;
  };
}

export interface FormTutorDTO {
  employeeId: number;
  firstName: string;
  lastName: string;
  dateFrom: string;
  dateTo: string;
}

export const classUnitFromDTO = (dto: ClassUnitDTO): ClassUnit => ({
  id: dto.id,
  alias: dto.alias,
  mark: dto.mark,
  startingClassificationPeriodId: dto.startingClassificationPeriodId,
  startingClassificationPeriodYear: dto.startingClassificationPeriodYear,
  startingClassificationPeriodNumber: dto.startingClassificationPeriodNumber,
  teachingCycleLength: dto.teachingCycleLength,
  level: dto.level,
  promoteEvery: dto.promoteEvery,
  formTutors: dto.formTutors.map(
    (t: FormTutorDTO): FormTutor => ({
      employeeId: t.employeeId,
      firstName: t.firstName,
      lastName: t.lastName,
      dateFrom: t.dateFrom,
      dateTo: t.dateTo,
    }),
  ),
  schoolUnit: {
    id: dto.schoolUnit.id,
    name: dto.schoolUnit.name,
    shortName: dto.schoolUnit.shortName,
  },
});
