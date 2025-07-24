import type { PaginatedResourceDTO } from "@/api/dto/paginated-resource.ts";
import type { PaginatedResourceEnity } from "@/api/entities/paginated-resource.ts";

export const paginationResourceDTOToEntity = <T, M>(
    dto: PaginatedResourceDTO<T>,
    mappedData: M[],
): PaginatedResourceEnity<M> => ({
    data: mappedData,
    pagination: {
        page: dto.currentPage,
        total: dto.total,
        perPage: dto.perPage,
    },
});
