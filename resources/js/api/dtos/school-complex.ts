import type { SchoolComplex, SchoolStructure } from '../types/school-structure';
import { type SchoolUnitDTO, schoolUnitFromDTO } from './school-unit';

export interface SchoolComplexDTO {
  id: number;
  name: string;
  type: number;
}

export const schoolComplexFromDTO = (
  dto: SchoolComplexDTO,
  unitDTOs: SchoolUnitDTO[],
): SchoolComplex => ({
  id: dto.id,
  name: dto.name,
  units: unitDTOs.map(schoolUnitFromDTO).sort((a, b) => a.id - b.id),
});

export function schoolStructureFromDTOs(
  complex: SchoolComplexDTO | null,
  units: SchoolUnitDTO[],
): SchoolStructure {
  if (complex !== null) {
    return {
      mode: 'multiple',
      complex: schoolComplexFromDTO(complex, units),
    };
  }
  return {
    mode: 'single',
    unit: schoolUnitFromDTO(units[0]!),
  };
}
