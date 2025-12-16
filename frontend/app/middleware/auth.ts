import { defineNuxtRouteMiddleware, navigateTo } from '#app'
import { useAuthStore } from '~/stores/authStore'

export default defineNuxtRouteMiddleware(async (to) => {
  const auth = useAuthStore()

  if (!auth.isAuthenticated) {
    await auth.fetchUser()
  }

  if (to.path.startsWith('/tasks') && !auth.isAuthenticated) {
    return navigateTo('/signin')
  }

  if (to.path === '/signin' && auth.isAuthenticated) {
    return navigateTo('/tasks')
  }
})
