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
  town: string | null;
  postalCode: string;
  street: string;
  houseNumber: string;
  flatNumber: string | null;
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
  town: dto.town,
  postalCode: dto.postalCode,
  street: dto.street,
  houseNumber: dto.houseNumber,
  flatNumber: dto.flatNumber,
  shortName: dto.shortName,
  active: dto.active,
});

export interface SchoolUnitBodyDTO {
  name: string;
  type: number;
  voivodeship: number;
  municipality: string;
  district: string | null;
  town: string | null;
  postalCode: string;
  street: string;
  houseNumber: string;
  flatNumber: string | null;
  studentCategory: string;
  shortName: string;
  schoolComplexId: number;
}
