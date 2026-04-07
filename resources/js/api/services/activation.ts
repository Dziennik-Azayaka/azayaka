import type { ActivationCodeAuthDTO } from '../dtos/activation-code-auth';
import type { ActivationStatusDTO } from '../dtos/activation-status';
import type { ActivationCodeAuth } from '../types/activation-code-auth';
import type { ActivationStatus } from '../types/activation-status';
import { http } from '@/config/ofetch';

export const ActivationService = {
  checkCode: (words: string[]): Promise<ActivationCodeAuth> =>
    http<ActivationCodeAuthDTO>('/activation/lookup', {
      method: 'POST',
      body: { code: words.join(',') },
    }),
  checkEmailAvailability: (email: string): Promise<{ available: boolean }> =>
    http<{ accountExist: boolean }>('/activation/emailAvailability', {
      method: 'POST',
      body: { email },
    }).then((res) => ({ available: !res.accountExist })),
  getStatus: (): Promise<ActivationStatus> =>
    http<ActivationStatusDTO>('/activation/status', {
      method: 'GET',
    }),
  activate: (words: string[], email: string, password: string) =>
    http('/activation', {
      method: 'POST',
      body: { code: words.join(','), email, password },
    }),
};
