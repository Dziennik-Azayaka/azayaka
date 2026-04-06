import type { ActivityLogEntry } from '../types/activity-log-entry';
import { UAParser } from 'ua-parser-js';

export interface ActivityLogEntryDTO {
  eventType:
    | 'failed_login_attempt'
    | 'successful_login_attempt'
    | 'logout'
    | 'credentials_changed'
    | 'logged_out_by_another_device';
  ip: string;
  userAgent: string;
  createdAt: string;
}

export const activityLogEntryFromDTO = (dto: ActivityLogEntryDTO): ActivityLogEntry => {
  const parsedUA = UAParser(dto.userAgent);
  const deviceInfo =
    parsedUA.device.vendor && parsedUA.device.model
      ? `${parsedUA.device.vendor} ${parsedUA.device.model}`
      : null;
  const browserInfo =
    parsedUA.browser.name && parsedUA.browser.version
      ? `${parsedUA.browser.name} ${parsedUA.browser.version}`
      : null;
  const osInfo =
    parsedUA.os.name && parsedUA.os.version ? `${parsedUA.os.name} ${parsedUA.os.version}` : null;
  return {
    type: dto.eventType,
    date: new Date(dto.createdAt),
    device: {
      ipAddress: dto.ip,
      name: deviceInfo,
      software: browserInfo,
      os: osInfo,
      rawUserAgent: dto.userAgent,
    },
  };
};
