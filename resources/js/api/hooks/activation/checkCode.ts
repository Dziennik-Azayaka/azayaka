import { ActivationService } from '../../services/activation';
import { useMutation } from '@tanstack/vue-query';

export const useCheckCode = () =>
  useMutation({
    mutationKey: ['checkCode'],
    mutationFn: (words: string[]) => ActivationService.checkCode(words),
  });
