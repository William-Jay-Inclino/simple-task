export interface ApiResponse<T = any> {
    data?: T
    message?: string
    errors?: Record<string, string[]>
}