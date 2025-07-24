export interface PaginatedResourceEnity<T> {
    pagination: {
        page: number;
        perPage: number;
        total: number;
    };
    data: T[];
}
