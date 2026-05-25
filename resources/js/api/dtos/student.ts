import type { Student } from '../types/student';
import { personFromDTO, type PersonDTO } from './person';

export interface StudentDTO {
  id: number;
  person: PersonDTO;
  admissionDate: string;
  leaveDate: string | null;
  leaveReason: string | null;
}

export const studentFromDTO = (dto: StudentDTO): Student => ({
  ...dto,
  admissionDate: new Date(dto.admissionDate),
  leaveDate: dto.leaveDate ? new Date(dto.leaveDate) : null,
  person: personFromDTO(dto.person),
});
