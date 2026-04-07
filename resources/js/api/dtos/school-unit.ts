import type { SchoolUnit } from '../types/school-structure';

export interface SchoolUnitDTO {
  id: number;
  name: string;
  type: number;
  studentCategory: 'childrenAndYouths' | 'adultsOnly';
  municipality: string;
  voivodeship: number;
  district: string | null;
  schoolComplexId: number;
  address: string;
  shortName: string;
  active: boolean;
}

export const schoolUnitFromDTO = (dto: SchoolUnitDTO): SchoolUnit => ({
  id: dto.id,
  name: dto.name,
  type: dto.type,
  studentCategory: dto.studentCategory,
  municipality: dto.municipality,
  voivodeship: dto.voivodeship,
  district: dto.district,
  schoolComplexId: dto.schoolComplexId,
  address: dto.address,
  shortName: dto.shortName,
  active: dto.active,
});

export interface SchoolUnitBodyDTO {
  name: string;
  type: number;
  address: string;
  voivodeship: number;
  municipality: string;
  district: string | null;
  studentCategory: string;
  shortName: string;
  schoolComplexId: number;
}
