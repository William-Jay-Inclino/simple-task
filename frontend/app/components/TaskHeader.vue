<script setup lang="ts">
import { LucideRecycle, LucideUser, LucideSearch, LucideLogOut } from 'lucide-vue-next'
import { useTaskStore } from '~/stores/taskStore'
import { useAuthStore } from '~/stores/authStore'

const taskStore = useTaskStore()
const authStore = useAuthStore()
const isDropdownOpen = ref(false)

const toggleDropdown = () => {
    isDropdownOpen.value = !isDropdownOpen.value
}

const handleLogout = async () => {
    await authStore.logout()
    await navigateTo('/sign-in')
}

// Close dropdown when clicking outside
const dropdownRef = ref<HTMLElement | null>(null)
onMounted(() => {
    const handleClickOutside = (event: MouseEvent) => {
        if (dropdownRef.value && !dropdownRef.value.contains(event.target as Node)) {
            isDropdownOpen.value = false
        }
    }
    document.addEventListener('click', handleClickOutside)
    onUnmounted(() => {
        document.removeEventListener('click', handleClickOutside)
    })
})
</script>

<template>
    <nav class="flex items-center justify-between border-b border-gray-200 bg-white px-6 py-4">
        <div class="flex items-center">
            <LucideRecycle :size="32" class="text-gray-900" />
        </div>
        
        <div class="flex-1 mx-8 max-w-md">
            <div class="relative">
                <LucideSearch :size="18" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
                <input
                    v-model="taskStore.searchQuery"
                    type="text"
                    placeholder="Search"
                    class="w-full rounded-lg border border-gray-200 bg-gray-50 py-2 pl-10 pr-4 text-sm focus:border-gray-300 focus:outline-none"
                />
            </div>
        </div>
        
        <div class="flex items-center relative" ref="dropdownRef">
            <button 
                @click="toggleDropdown"
                class="rounded-full bg-gray-900 p-2 text-white hover:bg-gray-800 transition-colors"
            >
                <LucideUser :size="20" />
            </button>

            <!-- Dropdown Menu -->
            <div 
                v-if="isDropdownOpen"
                class="absolute right-0 top-12 w-48 rounded-lg border border-gray-200 bg-white shadow-lg z-50"
            >
                <div class="p-2">
                    <button
                        @click="handleLogout"
                        class="flex w-full items-center gap-3 rounded-md px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors"
                    >
                        <LucideLogOut :size="16" />
                        <span>Logout</span>
                    </button>
                </div>
            </div>
        </div>
    </nav>
</template>
