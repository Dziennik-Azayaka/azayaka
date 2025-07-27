import { AlreadyTakenEmailAddressError, ApiError, IncorrectPasswordError } from "../errors";
import { activityLogEntryDTOToEntity } from "../mappers/activity-log-entry";

import axios from "@/api";
import type { ActivityLogEntryDTO } from "@/api/dto/activity-log-entry.ts";
import type { PaginatedResourceDTO } from "@/api/dto/paginated-resource.ts";
import { paginationResourceDTOToEntity } from "@/api/mappers/paginated-resource.ts";
import { AxiosError } from "axios";

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
		axios
			.put("/user/email", { email: newEmailAddress, password })
			.catch((reason) => {
				if (reason instanceof AxiosError) {
					console.log(reason.response?.data.errors)
					const errors = reason.response?.data.errors;
					if ('email' in errors && errors.email.includes("The email has already been taken.")) throw new AlreadyTakenEmailAddressError();
					if (Array.isArray(errors)) {
						if (errors.includes("WRONG_PASSWORD")) throw new IncorrectPasswordError();
					}
				}
				throw new ApiError(reason);
			})
};
