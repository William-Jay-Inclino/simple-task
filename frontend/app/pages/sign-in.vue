<script setup lang="ts">
import { ref } from 'vue'
import { useAuthStore } from '~/stores/authStore'
import type { LoginCredentials } from '~/types'
import { LucideRecycle } from 'lucide-vue-next'

// definePageMeta({
//     middleware: ['auth'],
// })

const authStore = useAuthStore()
const form = ref<LoginCredentials>({ email: '', password: '' })
const error = ref<string | null>(null)

const onSubmit = async () => {
    error.value = null
    const res = await authStore.login(form.value)
    if (res.success) {
        await navigateTo('/tasks')
    } else {
        error.value = res.error || 'Invalid credentials'
    }
}
</script>

<template>
    <div class="min-h-screen bg-white">
        <div class="mx-auto flex min-h-screen flex-col items-center justify-center px-4 py-10">
            <!-- Logo -->
            <div class="mb-6 flex justify-center">
                <LucideRecycle :size="48" class="text-gray-900" />
            </div>

            <!-- Card -->
            <div class="w-full rounded-xl border border-gray-200 bg-white p-6 shadow sm:p-8 max-w-md sm:max-w-lg min-h-[480px]">
                <h1 class="mb-2 text-center text-2xl font-semibold sm:text-3xl">Sign In</h1>
                <p class="mb-6 text-center text-sm text-gray-600">Login to continue using this app</p>

                <form @submit.prevent="onSubmit" class="space-y-4">
                    <div>
                        <label class="mb-1 block text-xs font-medium">Email</label>
                        <input
                            v-model="form.email"
                            type="email"
                            required
                            class="w-full rounded-lg border px-3 py-2 text-sm focus:border-gray-900 focus:outline-none"
                            placeholder="matt@goteam.com"
                        />
                    </div>

                    <div>
                        <div class="mb-1 flex items-center justify-between">
                            <label class="text-xs font-medium">Password</label>
                            <NuxtLink to="#" class="text-xs text-gray-600">Forgot your password?</NuxtLink>
                        </div>
                        <input
                            v-model="form.password"
                            type="password"
                            required
                            class="w-full rounded-lg border px-3 py-2 text-sm focus:border-gray-900 focus:outline-none"
                            placeholder="password"
                        />
                    </div>

                    <div v-if="error" class="rounded-lg border border-red-200 bg-red-50 p-3 text-sm">
                        {{ error }}
                    </div>

                    <button
                        type="submit"
                        class="flex w-full items-center justify-center rounded-full bg-black px-4 py-2 text-white"
                        :disabled="authStore.isLoading"
                    >
                        <span v-if="!authStore.isLoading">Login</span>
                        <span v-else>Signing in…</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>