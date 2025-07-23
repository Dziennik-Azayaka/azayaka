import type { ActivityLogEntryDTO } from "../dto/activity-log-entry";
import type { ActivityLogEntryEntity } from "../entities/activity-log-entry";
import { UAParser } from "ua-parser-js";

export const activityLogEntryDTOToEntity = (dto: ActivityLogEntryDTO): ActivityLogEntryEntity => {
    const parsedUA = UAParser(dto.userAgent);
    const deviceInfo =
        parsedUA.device.vendor && parsedUA.device.model ? `${parsedUA.device.vendor} ${parsedUA.device.model}` : null;
    const browserInfo =
        parsedUA.browser.name && parsedUA.browser.version
            ? `${parsedUA.browser.name} ${parsedUA.browser.version}`
            : null;
    const osInfo = parsedUA.os.name && parsedUA.os.version ? `${parsedUA.os.name} ${parsedUA.os.version}` : null;
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
