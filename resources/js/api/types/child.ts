import type { Person } from './person';

export interface CompulsoryEducationFulfillment {
  id: number;
  schoolYear: number;
  controlDate: Date;
  kindergartenInfo: string | null;
  postponementInfo: string | null;
  schoolInfo: string | null;
  outOfSchoolInfo: string | null;
  level: number;
}

export interface Child {
  id: number;
  person: Person;
  compulsoryEducationFulfillments: CompulsoryEducationFulfillment[];
}
