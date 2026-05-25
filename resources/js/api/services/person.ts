import type { CreatePersonBodyDTO } from '@/api/dtos/person';
import { http } from '@/config/ofetch';

export interface LookupPersonBody {
  pesel?: string;
  alternateIdentityDocument?: string;
}

export interface LookupFoundPerson {
  found: true;
  id: number;
  firstName: string;
  lastName: string;
  secondName: string | null;
  birthdate: string;
}

export interface LookupNotFoundPerson {
  found: false;
}

export type LookupPersonResponse = LookupFoundPerson | LookupNotFoundPerson;

export const PersonService = {
  lookup: (schoolUnitId: number, data: LookupPersonBody): Promise<LookupPersonResponse> =>
    http<LookupPersonResponse>(`/schoolUnits/${schoolUnitId}/people/lookup`, {
      method: 'POST',
      body: data,
    }),

  create: (
    schoolUnitId: number,
    data: CreatePersonBodyDTO,
  ): Promise<{ success: true; personId: number }> =>
    http(`/schoolUnits/${schoolUnitId}/people`, { method: 'POST', body: data }),

  update: (
    schoolUnitId: number,
    personId: number,
    data: CreatePersonBodyDTO,
  ): Promise<{ success: true }> =>
    http(`/schoolUnits/${schoolUnitId}/people/${personId}`, { method: 'PUT', body: data }),
};
