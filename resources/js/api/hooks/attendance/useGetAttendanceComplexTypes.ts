import { AttendanceService } from '../../services/attendance'
import { useQuery } from '@tanstack/vue-query'

export const useGetAttendanceComplexTypes = () =>
  useQuery({
    queryKey: ['getAttendanceComplexTypes'],
    queryFn: () => AttendanceService.getComplexTypes(),
    staleTime: Infinity,
  })
