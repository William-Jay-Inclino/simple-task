<script setup lang="ts">
import { useTaskStore } from '~/stores/taskStore'
import { formatDate } from '~/utils/date'

definePageMeta({
    middleware: ['auth'],
})

const taskStore = useTaskStore()
// const { getCsrfCookie } = useApi()

// onMounted(async () => {
//     await getCsrfCookie()
//     await taskStore.handleDateSelect(taskStore.selectedDate)
// })

// watch(() => taskStore.searchQuery.value, (newQuery) => {
//     taskStore.debouncedSearchFn(newQuery)
// })

const isEmpty = computed(() => taskStore.filteredTasks.length === 0)
</script>

<template>
    <div class="flex h-screen flex-col bg-gray-50">
        <TaskHeader />

        <!-- Main Layout -->
        <div class="flex flex-1 overflow-hidden">
            <TaskSidebar
                :selected-date="taskStore.selectedDate"
                @select-date="taskStore.handleDateSelect"
            />

            <!-- Main Content -->
            <main class="flex-1 overflow-y-auto bg-white p-8 pb-32">
				
                <!-- Empty State -->
                <div v-if="isEmpty" class="flex min-h-[400px] flex-col items-center justify-center">
                    <h2 class="mb-6 text-2xl font-semibold">What do you have in mind?</h2>
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
