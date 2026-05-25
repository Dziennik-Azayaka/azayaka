import { activityLogEntryFromDTO, type ActivityLogEntryDTO } from '../dtos/activity-log-entry';
import type { UserDTO } from '../dtos/user';
import type { ActivityLogEntry } from '../types/activity-log-entry';
import type { User } from '../types/user';
import { http } from '@/config/ofetch';

interface ActivityLogPaginatedResponse {
  currentPage: number;
  data: ActivityLogEntryDTO[];
  from: number;
  lastPage: number;
  perPage: number;
  to: number;
  total: number;
}

export const UserService = {
  getCurrent: (): Promise<User> => http<UserDTO>('/user', { method: 'GET' }),
  getActvityLog: (
    page: number,
  ): Promise<{
    data: ActivityLogEntry[];
    currentPage: number;
    perPage: number;
    total: number;
  }> =>
    http<ActivityLogPaginatedResponse>('/user/logs', {
      method: 'GET',
      query: { page },
    }).then((res) => ({
      ...res,
      data: res.data.map(activityLogEntryFromDTO),
    })),
  setEmail: (email: string, password: string) =>
    http('/user/email', {
      method: 'PUT',
      body: { email, password },
    }),
  setPassword: (oldPassword: string, newPassword: string) =>
    http('/user/password', {
      method: 'PUT',
      body: { oldPassword, newPassword },
    }),
};
