import type { SessionListEntry } from '../types/session-list';
import { UAParser } from 'ua-parser-js';

export interface SessionListDTO {
  currentSession: string;
  sessions: SessionListEntryDTO[];
}

export interface SessionListEntryDTO {
  id: string;
  ipAddress: string;
  userAgent: string;
  lastActivity: string;
}

export const sessionListEntryFromDTO = (dto: SessionListEntryDTO): SessionListEntry => {
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
    device: {
      ipAddress: dto.ipAddress,
      name: deviceInfo,
      software: browserInfo,
      os: osInfo,
      rawUserAgent: dto.userAgent,
    },
    id: dto.id,
    lastActivityDate: new Date(dto.lastActivity),
  };
};
