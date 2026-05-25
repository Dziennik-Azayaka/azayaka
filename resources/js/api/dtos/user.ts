export interface UserDTO {
  email: string;
  accesses: UserAccessDTO[];
}

export type UserAccessDTO = {
  id: number;
  name: string;
  type: 'employee' | 'student' | 'guardian';
  employeeId?: number | null;
  studentId?: number | null;
  guardianId?: number | null;
  modulesAvailable: Module[];
};

export type Module = 'student' | 'secretary' | 'administrator' | 'teacher';
