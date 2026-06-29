import type { SchoolUnit } from '../types/school-structure';

export interface SchoolUnitDTO {
  id: number;
  name: string;
  type: number;
  studentCategory: 'childrenAndYouths' | 'adultsOnly';
  municipality: string;
  voivodeship: number;
  town: string;
  district: string | null;
  postalCode: string;
  street: string | null;
  houseNumber: string;
  flatNumber: string | null;
  schoolComplexId: number;
  shortName: string;
  active: boolean;
}

export const schoolUnitFromDTO = (dto: SchoolUnitDTO): SchoolUnit => dto;

export interface SchoolUnitBodyDTO {
  name: string;
  type: number;
  address: string;
  voivodeship: number;
  town: string;
  municipality: string;
  district: string | null;
  postalCode: string;
  street: string | null;
  houseNumber: string;
  flatNumber: string | null;
  studentCategory: string;
  shortName: string;
  schoolComplexId: number | null;
}
