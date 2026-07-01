import type { Guardian } from "./guardian";
import type { ResidenceAddress } from "./residence-address";

export interface Person {
  id: number;
  firstName: string;
  secondName: string | null;
  lastName: string;
  pesel: string | null;
  alternateIdentityDocument: string | null;
  birthdate: string | null;
  birthplace: string | null;
  gender: string;
  residenceAddress: ResidenceAddress;
  guardians: Guardian[];
}
