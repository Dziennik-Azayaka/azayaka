import { type LookupPersonBody, PersonService } from '../../services/person';
import { useMutation } from '@tanstack/vue-query';

export const useLookupPerson = () =>
  useMutation({
    mutationKey: ['lookupPerson'],
    mutationFn: ({
      schoolUnitId,
      data,
    }: {
      schoolUnitId: number;
      data: LookupPersonBody;
    }) => PersonService.lookup(schoolUnitId, data),
  });
