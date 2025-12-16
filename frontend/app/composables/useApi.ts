import axios, { type AxiosInstance, type AxiosRequestConfig, type AxiosRequestHeaders } from 'axios'

export const useApi = () => {
    const config = useRuntimeConfig()
    const apiUrl = String(config.public.apiUrl)

    const client: AxiosInstance = axios.create({
        baseURL: `${apiUrl}/api`,
        withCredentials: true,
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
        xsrfCookieName: 'XSRF-TOKEN',
        xsrfHeaderName: 'X-XSRF-TOKEN',
    })

    // Helper to read a cookie value by name
    const getCookie = (name: string): string | null => {
        if (typeof document === 'undefined') return null
        const match = document.cookie.match(
            new RegExp('(^|; )' + name.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + '=([^;]*)')
        )
        return match && match[2] !== undefined ? decodeURIComponent(match[2]) : null
    }

    client.interceptors.request.use((config) => {
        const token = getCookie('XSRF-TOKEN')
        if (token) {
            const currentHeaders = (config.headers || {}) as AxiosRequestHeaders | Record<string, string>
            config.headers = {
                ...currentHeaders,
                'X-XSRF-TOKEN': token,
            } as unknown as AxiosRequestHeaders
        }
        return config
    })

    const apiFetch = async <T = unknown>(url: string, options?: AxiosRequestConfig): Promise<T> => {
        const res = await client.request<T>({ url, ...options })
        return res.data as T
    }

    const getCsrfCookie = async () => {
        await axios.get(`${apiUrl}/sanctum/csrf-cookie`, {
            withCredentials: true,
        })
    }

    return {
        apiFetch,
        getCsrfCookie,
    }
}