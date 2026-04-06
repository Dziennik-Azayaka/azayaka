import { UserService } from '@/api/services/user';
import type { User, UserAccess } from '@/api/types/user';
import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useUserStore = defineStore('user', () => {
  const user = ref<User | null>(null);
  const access = ref<UserAccess | null>(null);

  async function fetchUser() {
    try {
      user.value = await UserService.getCurrent();
    } catch {
      user.value = null;
    }
    return user.value;
  }

  async function getUser() {
    return user.value || (await fetchUser());
  }

  function setAccess(newAccess: UserAccess) {
    access.value = newAccess;
  }

  return { user, fetchUser, getUser, setAccess, access };
});
