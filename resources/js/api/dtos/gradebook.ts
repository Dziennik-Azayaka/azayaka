import type { Gradebook } from '../types/gradebook';
import { classUnitFromDTO, type ClassUnitDTO } from './class-unit';

export interface GradebookDTO {
  id: number;
  schoolYear: number;
  level: number | null;
  classUnit: ClassUnitDTO;
}

export const gradebookFromDTO = (dto: GradebookDTO): Gradebook => ({
  id: dto.id,
  schoolYear: dto.schoolYear,
  level: dto.level,
  classUnit: classUnitFromDTO(dto.classUnit),
});
