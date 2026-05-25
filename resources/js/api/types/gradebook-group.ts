export interface GradebookGroupTeacher {
  id: number;
  firstName: string;
  lastName: string;
}

export interface GradebookGroupSubject {
  id: number;
  description: string;
  subject: string;
  teachers: GradebookGroupTeacher[];
}

export interface GradebookGroup {
  id: number;
  name: string;
  shortcut: string;
  studentIds: number[];
  subjects: GradebookGroupSubject[];
}
