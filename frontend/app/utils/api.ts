import axios, { type AxiosInstance, type AxiosRequestConfig } from 'axios'

const TOKEN_KEY = 'simple_task_token'

const getRuntimeApiUrl = (): string => {
    try {
        if (typeof window === 'undefined') return process.env.NUXT_PUBLIC_API_URL ?? 'http://localhost'
        
        // This works if you use Nuxt 3/4 runtime config
        const config = useRuntimeConfig()
        return String(config.public?.apiUrl ?? 'http://localhost')
    } catch (e) {
        return 'http://localhost'
    }
}

const createClient = (): AxiosInstance => {
    const apiUrl = getRuntimeApiUrl()

    const client = axios.create({
        baseURL: `${apiUrl}/api`,
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        },
    })

    client.interceptors.request.use((config) => {
        if (typeof window !== 'undefined') {
            const token = localStorage.getItem(TOKEN_KEY)
            if (token && config.headers) {
                config.headers.Authorization = `Bearer ${token}`
            }
        }
        return config
    })

    client.interceptors.response.use(
        (response) => response,
        (error) => {
            if (error.response?.status === 401) {
                clearAuthToken()
                if (typeof window !== 'undefined') {
                    window.location.href = '/signin' 
                }
            }
            return Promise.reject(error)
        }
    )

    return client
}

export const apiClient = createClient()

export const setAuthToken = (token: string) => {
    if (typeof window !== 'undefined') {
        localStorage.setItem(TOKEN_KEY, token)
    }
}

export const getAuthToken = (): string | null => {
    if (typeof window === 'undefined') return null
    return localStorage.getItem(TOKEN_KEY)
}

export const clearAuthToken = () => {
    if (typeof window !== 'undefined') {
        localStorage.removeItem(TOKEN_KEY)
    }
}

export const apiFetch = async <T = any>(
    url: string, 
    options?: AxiosRequestConfig
): Promise<T> => {
    const res = await apiClient.request<T>({
        url,
        ...options,
    })
    return res.data
}

export default apiClient