import { toTypedSchema } from '@vee-validate/valibot';
import * as v from 'valibot';

export const setUnitActivityForm = toTypedSchema(
  v.object({
    password: v.pipe(v.string(), v.nonEmpty()),
  }),
);
