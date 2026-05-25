import type { Person } from '../types/person';
import type { GuardianDTO } from './guardian';
import type { ResidenceAddressDTO } from './residence-address';

export interface PersonDTO {
  id: number;
  firstName: string;
  secondName: string | null;
  lastName: string;
  pesel: string | null;
  alternateIdentityDocument: string | null;
  birthdate: string;
  birthplace: string | null;
  gender: string;
  residenceAddress: ResidenceAddressDTO;
  guardians: GuardianDTO[];
}

export const personFromDTO = (dto: PersonDTO): Person => ({
  ...dto,
  birthdate: new Date(dto.birthdate),
});

export interface CreatePersonBodyDTO {
  firstName: string;
  lastName: string;
  secondName?: string | null;
  pesel?: string;
  alternateIdentityDocument?: string;
  birthdate: string;
  birthplace: string;
  gender?: 'male' | 'female' | null;
  studentRegistryId?: number;
  studentRegistryNumber?: number;
  admissionDate?: string;
  childrenRegistryId?: number;
  residenceAddressCountry: string;
  residenceAddressCommune?: string | null;
  residenceAddressTown?: string | null;
  residenceAddressPostalCode?: string | null;
  residenceAddressStreet?: string | null;
  residenceAddressHouseNumber?: string | null;
  residenceAddressFlatNumber?: string | null;
}
