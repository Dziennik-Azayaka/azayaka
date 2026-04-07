import { toTypedSchema } from '@vee-validate/valibot';
import * as v from 'valibot';

export const checkEmailAvailabilitySchema = toTypedSchema(
  v.object({
    email: v.pipe(v.string(), v.email()),
  }),
);
