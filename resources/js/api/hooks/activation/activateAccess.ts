import { ActivationService } from '../../services/activation';
import { useMutation } from '@tanstack/vue-query';

export const useActivateAccess = () =>
  useMutation({
    mutationKey: ['activateAccess'],
    mutationFn: ({
      words,
      email,
      password,
    }: {
      words: string[];
      email: string;
      password: string;
    }) => ActivationService.activate(words, email, password),
  });
