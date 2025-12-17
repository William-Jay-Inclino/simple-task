<script setup lang="ts">
import { LucideRecycle, LucideUser, LucideSearch, LucideLogOut, LucideMenu } from 'lucide-vue-next'
import { useTaskStore } from '~/stores/taskStore'
import { useAuthStore } from '~/stores/authStore'

const emit = defineEmits<{
    toggleSidebar: []
}>()

const taskStore = useTaskStore()
const authStore = useAuthStore()
const isDropdownOpen = ref(false)

const toggleDropdown = () => {
    isDropdownOpen.value = !isDropdownOpen.value
}

const handleLogout = async () => {
    await authStore.logout()
    taskStore.$reset()
    await navigateTo('/simple-task/signin')
}

const handleToggleSidebar = () => {
    emit('toggleSidebar')
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
    <nav class="flex items-center justify-between border-b border-gray-200 bg-white px-3 py-3 sm:px-4 sm:py-4 md:px-6">
        <div class="flex items-center gap-2 sm:gap-3">
            <!-- Mobile Menu Toggle -->
            <button
                @click="handleToggleSidebar"
                class="rounded-lg p-2 text-gray-900 hover:bg-gray-100 transition-colors lg:hidden"
                aria-label="Toggle sidebar"
            >
                <LucideMenu :size="24" />
            </button>
            
            <LucideRecycle :size="28" class="text-gray-900 sm:w-8 sm:h-8" />
        </div>
        
        <div class="flex-1 mx-2 max-w-md sm:mx-4 md:mx-8">
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
                aria-label="User menu"
            >
                <LucideUser :size="20" />
            </button>

            <!-- Dropdown Menu -->
            <Transition
                enter-active-class="transition-all duration-200"
                leave-active-class="transition-all duration-200"
                enter-from-class="opacity-0 scale-95"
                leave-to-class="opacity-0 scale-95"
            >
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
            </Transition>
        </div>
    </nav>
</template>
