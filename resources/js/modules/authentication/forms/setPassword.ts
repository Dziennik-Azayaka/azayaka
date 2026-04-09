import { toTypedSchema } from '@vee-validate/valibot';
import * as v from 'valibot';

export const setPasswordSchema = toTypedSchema(
  v.pipe(
    v.object({
      password: v.pipe(v.string(), v.minLength(8), v.maxLength(255)),
      repeatPassword: v.string(),
    }),
    v.check(
      (input) => input.password === input.repeatPassword,
      'validationErrors.passwordsMustMatch',
    ),
    v.forward(
      v.check(
        (input) => input.password === input.repeatPassword,
        'validationErrors.passwordsMustMatch',
      ),
      ['repeatPassword'] as const,
    ),
  ),
);
