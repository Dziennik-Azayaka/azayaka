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
