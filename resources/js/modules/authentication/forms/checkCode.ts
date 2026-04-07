import { toTypedSchema } from '@vee-validate/valibot';
import * as v from 'valibot';

export const checkCodeSchema = toTypedSchema(
  v.object({
    words: v.pipe(v.array(v.pipe(v.string(), v.nonEmpty())), v.length(10)),
  }),
);
