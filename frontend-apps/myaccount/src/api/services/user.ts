import { ApiError } from "../errors";
import { activityLogEntryDTOToEntity } from "../mappers/activity-log-entry";

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
            .catch(({ reason }) => {
                throw new ApiError(reason);
            }),
};
