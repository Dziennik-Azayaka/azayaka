import { AxiosError } from "axios";

import axios from "@/api";
import type { SubjectDTO } from "@/api/dto/subject.ts";
import type { SubjectEntity } from "@/api/entities/subject.ts";
import { ApiError, TakenNameError, TakenShortcutError } from "@/api/errors.ts";
import type { SubjectForm } from "@/types.ts";

export default {
    getAllSubjects: () => axios.get<SubjectDTO[]>("/subjects").then((res): SubjectEntity[] => res.data),
    addSubject: (subject: SubjectForm) =>
        axios.post("/subjects", subject).catch((reason) => {
            if (reason instanceof AxiosError) {
                const errors = reason.response?.data.errors;
                if (Array.isArray(errors)) {
                    if (errors.includes("SHORTCUT_TAKEN")) throw new TakenShortcutError();
                    if (errors.includes("NAME_TAKEN")) throw new TakenNameError();
                }
            }
            throw new ApiError(reason);
        }),
    editSubject: (id: number, subject: SubjectForm) =>
        axios.put<SubjectDTO>(`/subjects/${id}`, subject).catch((reason) => {
            if (reason instanceof AxiosError) {
                const errors = reason.response?.data.errors;
                if (Array.isArray(errors)) {
                    if (errors.includes("SHORTCUT_TAKEN")) throw new TakenShortcutError();
                    if (errors.includes("NAME_TAKEN")) throw new TakenNameError();
                }
            }
            throw new ApiError(reason);
        }),
};
