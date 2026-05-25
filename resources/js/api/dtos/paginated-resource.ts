export interface PaginatedResourceDTO<T> {
  data: T[];
  meta: {
    current_page: number;
    last_page: number;
  };
}
