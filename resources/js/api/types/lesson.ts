export interface TeacherInfo {
  id: number;
  firstName: string;
  secondName: string | null;
  lastName: string;
}

export interface GroupInfo {
  id: number;
  name: string;
  shortcut: string;
}

export interface Lesson {
  id: number;
  number: number;
  date: Date;
  primaryTeacher: TeacherInfo;
  assistingTeachers: TeacherInfo[];
  groups: GroupInfo[];
  subject: string;
  topic: string;
  startTime: string;
  endTime: string;
  completed: boolean;
}
