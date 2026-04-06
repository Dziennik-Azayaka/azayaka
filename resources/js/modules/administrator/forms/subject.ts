import { toTypedSchema } from '@vee-validate/valibot';
import * as v from 'valibot';

const subjectForm = v.object({
  name: v.pipe(v.string(), v.minLength(3), v.maxLength(255)),
  shortcut: v.pipe(v.string(), v.nonEmpty(), v.maxLength(32)),
});

export const subjectFormSchema = toTypedSchema(subjectForm);
export type SubjectValues = v.InferOutput<typeof subjectForm>;
