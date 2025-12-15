import { defineStore } from 'pinia'
import type { User, LoginCredentials } from '~/types'
import { useApi } from '~/composables/useApi'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null as User | null,
    isAuthenticated: false,
    isLoading: false,
  }),

  actions: {
    async login(credentials: LoginCredentials) {
      const { apiFetch, getCsrfCookie } = useApi()
      
      try {
        this.isLoading = true
        // Ensure CSRF cookie is set
        await getCsrfCookie()
        // Perform login via Axios
        const response = await apiFetch<{ user: User; message: string }>('/login', {
          method: 'POST',
          data: credentials,
        })
        
        this.user = response.user
        this.isAuthenticated = true
        
        return { success: true }
      } catch (error: any) {
        this.user = null
        this.isAuthenticated = false
        
        return {
          success: false,
          error: error?.data?.message || 'Login failed. Please check your credentials.',
        }
      } finally {
        this.isLoading = false
      }
    },

    async logout() {
      const { apiFetch } = useApi()
      
      try {
        await apiFetch('/logout', { method: 'POST' })
      } catch (error) {
        console.error('Logout error:', error)
      } finally {
        this.user = null
        this.isAuthenticated = false
      }
    },

    async fetchUser() {
      const { apiFetch, getCsrfCookie } = useApi()
      
      try {
        // Ensure CSRF cookie is set before fetching user
        await getCsrfCookie()
        const response = await apiFetch<{ user: User }>('/user', { method: 'GET' })
        this.user = response.user
        this.isAuthenticated = true
      } catch (error) {
        this.user = null
        this.isAuthenticated = false
      }
    },
  },
})
