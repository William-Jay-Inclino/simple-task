import { defineStore } from 'pinia'
import type { User, LoginCredentials } from '~/types'
import { setAuthToken, apiFetch, clearAuthToken } from '~/utils'


export const useAuthStore = defineStore('auth', () => {

    const user = ref<User | null>(null)
    const isAuthenticated = ref(false)
    const isLoading = ref(false)

    async function login(credentials: LoginCredentials): Promise<{
        success: boolean, 
        message: string
    }> {
        try {
            isLoading.value = true
            const response = await apiFetch<{ user?: User, token?: string, message: string }>('/login', {
                method: 'POST',
                data: credentials,
            })

            if(response.user && response.token) {
                setAuthToken(response.token)
                user.value = response.user
                isAuthenticated.value = true
    
                return { success: true, message: response.message }
            }

        } catch (error: any) {
            user.value = null
            isAuthenticated.value = false
        }
        return {
            success: false,
            message: 'Login failed. Please check your credentials.',
        }
    }

    async function logout() {
        try {
            await apiFetch('/logout', { method: 'POST' })
        } catch (error) {
            console.error('Logout error:', error)
        } finally {
            clearAuthToken()
            $reset()
        }
    }

    async function fetchUser() {
        try {
            const response = await apiFetch<{ user: User }>('/user', { method: 'GET' })
            user.value = response.user
            isAuthenticated.value = true
        } catch (error) {
            user.value = null
            isAuthenticated.value = false
        }
    }

    function $reset() {
        user.value = null
        isAuthenticated.value = false
        isLoading.value = false
    }

    return {
        user,
        isAuthenticated,
        isLoading,
        login,
        logout,
        fetchUser,
    }
},
    { persist: true } 
)