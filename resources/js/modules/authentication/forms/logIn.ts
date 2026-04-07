import { toTypedSchema } from '@vee-validate/valibot';
import * as v from 'valibot';

export const logInSchema = toTypedSchema(
  v.object({
    email: v.pipe(v.string(), v.email()),
    password: v.pipe(v.string(), v.nonEmpty()),
  }),
);
