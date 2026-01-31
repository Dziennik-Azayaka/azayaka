import axios from "@/api";
import type { SubjectDTO } from "@/api/dto/subject.ts";
import type { SubjectEntity } from "@/api/entities/subject.ts";
import type { SubjectForm } from "@/types.ts";

export default {
    getAllSubjects: () => axios.get<SubjectDTO[]>("/subjects").then((res): SubjectEntity[] => res.data),
    addSubject: (subject: SubjectForm) => axios.post("/subjects", subject),
    editSubject: (id: number, subject: SubjectForm) => axios.put<SubjectDTO>(`/subjects/${id}`, subject),
    changeSubjectActivity: (id: number) => axios.put(`/subjects/${id}/activity`),
};
