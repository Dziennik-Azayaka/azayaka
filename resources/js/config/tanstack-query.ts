import { QueryClient, VueQueryPlugin } from '@tanstack/vue-query';

export const queryPlugin = VueQueryPlugin;

export const queryClient = new QueryClient({
  defaultOptions: {
    queries: {
      networkMode: 'always',
      retry: 0,
      refetchOnWindowFocus: false
    },
    mutations: {
      networkMode: 'always',
      retry: 0,
    },
  },
});
