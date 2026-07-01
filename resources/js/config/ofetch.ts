import { ApiError } from '@/api/error';
import { apiUrl } from '@/env';
import router from '@/router';
import { useUserStore } from '@/stores/user';
import { ofetch } from 'ofetch';

export const http = ofetch.create({
  baseURL: apiUrl,
  credentials: 'include',
  retry: false,
  parseResponse: JSON.parse,
  onResponseError: async ({ response, request }) => {
    const error = ApiError.fromResponse(response);
    if (error.code === 'USER_NOT_LOGGED_IN' && request.toString() !== '/user') {
      const userStore = useUserStore();
      userStore.user = null;

      await router.push({ name: 'auth.logIn' });
    }
    if (error.code === 'INVALID_ACCESS_ID_OR_INSUFFICIENT_PRIVILEGES') {
      await router.push({ name: 'myAccount' });
    }
    return Promise.reject(error);
  },
  onRequest: ({ options }) => {
    const userStore = useUserStore();
    if (userStore.access) options.headers.set('Access-ID', userStore.access.id.toString());

    if (options.query) {
      options.query = Object.fromEntries(
        Object.entries(options.query).filter(([_, v]) => v !== null)
      )
    }
  },
});
