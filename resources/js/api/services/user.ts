import { type ActivityLogEntryDTO, activityLogEntryFromDTO } from '../dtos/activity-log-entry';
import type { PaginatedResourceDTO } from '../dtos/paginated-resource';
import type { UserDTO } from '../dtos/user';
import type { ActivityLogEntry } from '../types/activity-log-entry';
import type { PaginatedResource } from '../types/paginated-resource';
import type { User } from '../types/user';
import { http } from '@/config/ofetch';

export const UserService = {
  getCurrent: (): Promise<User> => http<UserDTO>('/user', { method: 'GET' }),
  getActvityLog: (page: number): Promise<PaginatedResource<ActivityLogEntry>> =>
    http<PaginatedResourceDTO<ActivityLogEntryDTO>>('/user/logs', {
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
