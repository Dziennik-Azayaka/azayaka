import type { GuardianDTO } from "./guardian";
import type { ResidenceAddressDTO } from "./residence-address";

export interface PersonDTO {
  id: number;
  firstName: string;
  secondName: string | null;
  lastName: string;
  pesel: string | null;
  alternateIdentityDocument: string | null;
  birthdate: string | null;
  birthplace: string | null;
  gender: string;
  residenceAddress: ResidenceAddressDTO;
  guardians: GuardianDTO[];
}
