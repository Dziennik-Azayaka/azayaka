import { activityLogEntryDTOToEntity } from "../mappers/activity-log-entry";
import { userDTOToEntity } from "../mappers/user";

import axios from "@/api";
import type { ActivityLogEntryDTO } from "@/api/dto/activity-log-entry.ts";
import type { PaginatedResourceDTO } from "@/api/dto/paginated-resource.ts";
import { paginationResourceDTOToEntity } from "@/api/mappers/paginated-resource.ts";

export default {
    getUserInfo: () => axios.get("/user").then(({ data }) => userDTOToEntity(data)),
    getActivityLog: (page: number) =>
        axios
            .get<PaginatedResourceDTO<ActivityLogEntryDTO>>("/user/logs", { params: { page } })
            .then(({ data: response }) =>
                paginationResourceDTOToEntity(response, response.data.map(activityLogEntryDTOToEntity)),
            ),
    setNewEmailAddress: (newEmailAddress: string, password: string) =>
        axios.put("/user/email", { email: newEmailAddress, password }),
    setNewPassword: (currentPassword: string, newPassword: string) =>
        axios.put("/user/password", { oldPassword: currentPassword, newPassword: newPassword }),
};
