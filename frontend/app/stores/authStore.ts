import { defineStore } from 'pinia'
import type { User, LoginCredentials } from '~/types'
import { useApi } from '~/composables/useApi'

export const useAuthStore = defineStore('auth', () => {
    const { apiFetch, getCsrfCookie } = useApi()

    const user = ref<User | null>(null)
    const isAuthenticated = ref(false)
    const isLoading = ref(false)

    async function login(credentials: LoginCredentials) {
        try {
            isLoading.value = true
            // Ensure CSRF cookie is set
            await getCsrfCookie()
            // Perform login via Axios
            const response = await apiFetch<{ user: User; message: string }>('/login', {
                method: 'POST',
                data: credentials,
            })

            user.value = response.user
            isAuthenticated.value = true

            return { success: true }
        } catch (error: any) {
            user.value = null
            isAuthenticated.value = false

            return {
                success: false,
                error: error?.data?.message || 'Login failed. Please check your credentials.',
            }
        } finally {
            isLoading.value = false
        }
    }

    async function logout() {
        try {
            await apiFetch('/logout', { method: 'POST' })
        } catch (error) {
            console.error('Logout error:', error)
        } finally {
            user.value = null
            isAuthenticated.value = false
        }
    }

    async function fetchUser() {
        try {
            // Ensure CSRF cookie is set before fetching user
            await getCsrfCookie()
            const response = await apiFetch<{ user: User }>('/user', { method: 'GET' })
            user.value = response.user
            isAuthenticated.value = true
        } catch (error) {
            user.value = null
            isAuthenticated.value = false
        }
    }

    return {
        user,
        isAuthenticated,
        isLoading,
        login,
        logout,
        fetchUser,
    }
}, {
    persist: true,
})