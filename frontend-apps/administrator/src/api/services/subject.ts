import axios from "@/api";
import type { SubjectDTO } from "@/api/dto/subject.ts";
import type { SubjectEntity } from "@/api/entities/subject.ts";

export default {
    getAllSubjects: () =>
        axios.get<SubjectDTO[]>("/subjects").then((res): SubjectEntity[] => res.data),
};
