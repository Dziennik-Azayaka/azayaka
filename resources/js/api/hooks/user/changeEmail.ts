import { UserService } from '../../services/user';
import { useUserStore } from '@/stores/user';
import { useMutation } from '@tanstack/vue-query';

export const useChangeEmail = () => {
  const userStore = useUserStore();

  return useMutation({
    mutationKey: ['changeUserEmail'],
    mutationFn: ({ email, password }: { email: string; password: string }) =>
      UserService.setEmail(email, password),
    onSuccess: (_, { email }) => {
      if (!userStore.user) return;
      userStore.user.email = email;
    },
  });
};
