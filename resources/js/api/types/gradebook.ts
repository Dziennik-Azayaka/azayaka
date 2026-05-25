import type { ClassUnit } from './class-unit';

export interface Gradebook {
  id: number;
  schoolYear: number;
  level: number | null;
  classUnit: ClassUnit;
}
