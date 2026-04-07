export interface SubjectDTO {
  id: number;
  shortcut: string;
  name: string;
  active: boolean;
}

export interface SubjectBodyDTO {
  shortcut: string;
  name: string;
}
