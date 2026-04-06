import { SessionService } from '../../services/session';
import { useMutation } from '@tanstack/vue-query';

export const useLogIn = () =>
  useMutation({
    mutationKey: ['logIn'],
    mutationFn: ({ email, password }: { email: string; password: string }) =>
      SessionService.logIn(email, password),
  });
