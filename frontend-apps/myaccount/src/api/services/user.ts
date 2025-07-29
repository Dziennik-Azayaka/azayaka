import { AlreadyTakenEmailAddressError, ApiError, IncorrectPasswordError } from "../errors";
import { activityLogEntryDTOToEntity } from "../mappers/activity-log-entry";
import { AxiosError } from "axios";

import axios from "@/api";
import type { ActivityLogEntryDTO } from "@/api/dto/activity-log-entry.ts";
import type { PaginatedResourceDTO } from "@/api/dto/paginated-resource.ts";
import { paginationResourceDTOToEntity } from "@/api/mappers/paginated-resource.ts";

export default {
    getActivityLog: (page: number) =>
        axios
            .get<PaginatedResourceDTO<ActivityLogEntryDTO>>("/user/logs", { params: { page } })
            .then(({ data: response }) =>
                paginationResourceDTOToEntity(response, response.data.map(activityLogEntryDTOToEntity)),
            )
            .catch((reason) => {
                throw new ApiError(reason);
            }),
    setNewEmailAddress: (newEmailAddress: string, password: string) =>
        axios.put("/user/email", { email: newEmailAddress, password }).catch((reason) => {
            if (reason instanceof AxiosError) {
                const errors = reason.response?.data.errors;
                if (Array.isArray(errors)) {
                    if (errors.includes("EMAIL_TAKEN")) throw new AlreadyTakenEmailAddressError();
                    if (errors.includes("WRONG_PASSWORD")) throw new IncorrectPasswordError();
                }
            }
            throw new ApiError(reason);
        }),
    setNewPassword: (currentPassword: string, newPassword: string) =>
        axios.put("/user/password", { oldPassword: currentPassword, newPassword: newPassword }).catch((reason) => {
            if (reason instanceof AxiosError) {
                const errors = reason.response?.data.errors;
                if (Array.isArray(errors)) {
                    if (errors.includes("WRONG_PASSWORD")) throw new IncorrectPasswordError();
                }
            }
            throw new ApiError(reason);
        }),
};
