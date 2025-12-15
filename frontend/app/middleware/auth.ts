import { defineNuxtRouteMiddleware, navigateTo } from '#app'
import { useAuthStore } from '~/stores/authStore'

export default defineNuxtRouteMiddleware(async (to) => {
  const auth = useAuthStore()

  if (!auth.isAuthenticated) {
    await auth.fetchUser()
  }

  if (to.path.startsWith('/tasks') && !auth.isAuthenticated) {
    return navigateTo('/sign-in')
  }

  if (to.path === '/sign-in' && auth.isAuthenticated) {
    return navigateTo('/tasks')
  }
})
