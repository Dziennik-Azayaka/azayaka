import { ActivationService } from '../../services/activation';
import { useMutation } from '@tanstack/vue-query';

export const checkEmailAvailbility = () =>
  useMutation({
    mutationKey: ['checkEmailAvailbility'],
    mutationFn: (email: string) => ActivationService.checkEmailAvailability(email),
  });
