import { type SessionListDTO, sessionListEntryFromDTO } from '../dtos/session-list';
import type { SessionList } from '../types/session-list';
import { http } from '@/config/ofetch';

export const SessionService = {
  logIn: (email: string, password: string) =>
    http('/login', {
      method: 'POST',
      body: { email, password },
    }),
  getActive: (): Promise<SessionList> =>
    http<SessionListDTO>('/sessions', {
      method: 'GET',
    }).then((res) => ({
      ...res,
      sessions: res.sessions.map(sessionListEntryFromDTO),
    })),
  removeById: (id: string, password: string) =>
    http('/sessions/remove', {
      method: 'DELETE',
      body: { id, password },
    }),
};
