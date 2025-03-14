import type { AxiosInstance } from 'axios';

export type CRUDableComposable<T> = {
    create: (payload: object) => Promise<object>
    update: (hash: string, payload: object) => Promise<object>
    destroy: (hash: string) => Promise<boolean>
    list: (payload: object) => Promise<PaginatedList<T>>
    listAll: (payload: object) => Promise<ItemList<T>>
    updateMany: (payload: object) => Promise<object>
    show: (hash: string, query_parameters?: object) => Promise<object>
    restore: (hash: string) => Promise<boolean>
    $api?: AxiosInstance
}

export type ItemList<T> = {
    data: T[]
}

export type PaginatedList<T> = {
    current_page: number
    data: T[]
    from: number
    to: number
    total: number
    last_page: number
    per_page: number
    path: string | null
}