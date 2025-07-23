export interface PaginatedResourceDTO<T> {
    currentPage: number;
    data: T[];
    from: number;
    perPage: number;
    to: number;
    total: number;
}
