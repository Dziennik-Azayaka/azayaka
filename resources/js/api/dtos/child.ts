import type { Child, CompulsoryEducationFulfillment } from '../types/child';
import type { PersonDTO } from './person';
import { personFromDTO } from './person';

export interface CompulsoryEducationFulfillmentDTO {
  id: number;
  schoolYear: number;
  controlDate: string;
  kindergartenInfo: string | null;
  postponementInfo: string | null;
  schoolInfo: string | null;
  outOfSchoolInfo: string | null;
  level: number;
}

export interface ChildDTO {
  id: number;
  person: PersonDTO;
  compulsoryEducationFulfillments: CompulsoryEducationFulfillmentDTO[];
}

export const compulsoryEducationFulfillmentFromDTO = (
  dto: CompulsoryEducationFulfillmentDTO,
): CompulsoryEducationFulfillment => ({
  ...dto,
  controlDate: new Date(dto.controlDate),
});

export const childFromDTO = (dto: ChildDTO): Child => ({
  ...dto,
  person: personFromDTO(dto.person),
  compulsoryEducationFulfillments: dto.compulsoryEducationFulfillments.map(
    compulsoryEducationFulfillmentFromDTO,
  ),
});
