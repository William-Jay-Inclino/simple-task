<script setup lang="ts">
import { useTaskStore } from '~/stores/taskStore'

definePageMeta({
    middleware: ['auth'],
})

const taskStore = useTaskStore()
const isSidebarOpen = ref(false)

const isEmpty = computed(() => taskStore.filteredTasks.length === 0)

const toggleSidebar = () => {
    isSidebarOpen.value = !isSidebarOpen.value
}

const closeSidebar = () => {
    isSidebarOpen.value = false
}
</script>

<template>
    <div class="flex h-screen flex-col bg-gray-50">
        <TaskHeader @toggle-sidebar="toggleSidebar" />

        <!-- Main Layout -->
        <div class="flex flex-1 overflow-hidden">
            <!-- Overlay for mobile -->
            <Transition
                enter-active-class="transition-opacity duration-200"
                leave-active-class="transition-opacity duration-200"
                enter-from-class="opacity-0"
                leave-to-class="opacity-0"
            >
                <div
                    v-if="isSidebarOpen"
                    class="fixed inset-0 z-30 bg-black/50 lg:hidden"
                    @click="closeSidebar"
                />
            </Transition>

            <TaskSidebar
                :selected-date="taskStore.selectedDate"
                :is-open="isSidebarOpen"
                @select-date="taskStore.handleDateSelect"
                @close="closeSidebar"
            />

            <!-- Main Content -->
            <main class="flex-1 overflow-y-auto bg-white p-4 pb-32 sm:p-6 md:p-8">
				
                <!-- Empty State -->
                <div v-if="isEmpty" class="flex min-h-[400px] flex-col items-center justify-center px-4">
                    <h2 class="mb-6 text-xl font-semibold text-center sm:text-2xl">What do you have in mind?</h2>
                    <div class="w-full max-w-xl">
                        <TaskForm
                            placeholder="Write the task you plan to do today here..."
                            position="top"
                            :rows="4"
                        />
                    </div>
                </div>

                <TaskList v-else />
                <TaskForm v-if="!isEmpty" />

            </main>
        </div>
    </div>
</template>
