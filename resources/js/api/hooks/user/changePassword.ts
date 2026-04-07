import { UserService } from '../../services/user';
import { useMutation } from '@tanstack/vue-query';

export const useChangePassword = () =>
  useMutation({
    mutationKey: ['changeUserPassword'],
    mutationFn: ({ oldPassword, newPassword }: { oldPassword: string; newPassword: string }) =>
      UserService.setPassword(oldPassword, newPassword),
  });
