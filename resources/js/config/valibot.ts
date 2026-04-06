import { i18n } from './i18n';
import { setGlobalMessage } from 'valibot';

setGlobalMessage((issue) => {
  const t = i18n.global.t;
  const { kind, expected, received, type, requirement } = issue;

  if ((kind === 'schema' && received === 'undefined') || type === 'non_empty') {
    return t('validationErrors.fieldRequired');
  }

  if (type === 'email') {
    return t('validationErrors.invalidEmail');
  }

  if (type === 'min_length') {
    return t('validationErrors.minLength', { number: expected });
  }

  if (type === 'max_length') {
    return t('validationErrors.maxLength', { number: expected });
  }

  if (type === 'min_value') {
    return t('validationErrors.minValue', { number: requirement });
  }

  if (type === 'max_value') {
    return t('validationErrors.maxValue', { number: requirement });
  }

  return issue.message;
});
