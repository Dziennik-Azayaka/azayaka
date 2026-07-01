import type { Person } from "./person";

export interface Student {
  id: number;
  person: Person;
  admissionDate: Date;
  leaveDate: Date | null;
  leaveReason: string | null;
}
