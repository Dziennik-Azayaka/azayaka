export interface Class {
  id: number;
  schoolUnitId: number;
  alias: string;
  mark: string;
  startingClassificationPeriodId: number;
  startingClassificationPeriodYear: number;
  startingClassificationPeriodNumber: number;
  teachingCycleLength: number;
  level: number;
  formTutors: FormTutor[];
}

export interface FormTutor {
  employeeId: number;
  firstName: string;
  lastName: string;
  dateFrom: Date;
  dateTo: Date;
}
