<script setup lang="ts">
import { useTaskStore } from '~/stores/taskStore'
import { showConfirmDelete } from '~/utils/swal'
import { VueDraggable } from 'vue-draggable-plus'
import { formatDate } from '~/utils/date'

const taskStore = useTaskStore()

const handleToggleTask = (taskId: number) => {
    taskStore.toggleTaskCompletion(taskId)
}

const handleDeleteTask = async (taskId: number) => {
    const result = await showConfirmDelete({
        title: 'Delete Task?',
        text: 'This task will be permanently deleted.',
    })

    if (result.isConfirmed) {
        await taskStore.deleteTask(taskId)
    }
}

const onDragEnd = async () => {
    // Get the current task IDs in the new order
    const taskIds = taskStore.filteredTasks.map(task => task.id)
    
    // Determine the date for reordering
    let date: string
    if (taskStore.selectedDate === 'today') {
        const today = new Date()
        date = formatDate(today)
    } else if (taskStore.selectedDate === 'yesterday') {
        const yesterday = new Date()
        yesterday.setDate(yesterday.getDate() - 1)
        date = formatDate(yesterday)
    } else {
        date = taskStore.selectedDate
    }
    
    try {
        await taskStore.reorderTasks(taskIds, date)
    } catch (error) {
        console.error('Failed to reorder tasks:', error)
    }
}
</script>

<template>
    <div class="mx-auto max-w-3xl">
        <div v-if="taskStore.filteredTasks.length === 0" class="flex min-h-[400px] flex-col items-center justify-center px-4">
            <h2 class="text-xl font-semibold text-gray-400 text-center sm:text-2xl">No tasks found</h2>
            <p class="mt-2 text-sm text-gray-500 text-center">Add a new task to get started</p>
        </div>

        <VueDraggable
            v-else
            v-model="taskStore.tasks"
            :animation="150"
            :disabled="!!taskStore.searchQuery.trim()"
            handle=".drag-handle"
            @end="onDragEnd"
            class="space-y-4"
            :class="{ 'cursor-not-allowed': !!taskStore.searchQuery.trim() }"
        >
            <TaskListItem
                v-for="task in taskStore.filteredTasks"
                :key="task.id"
                :task="task"
                @toggle="handleToggleTask"
                @delete="handleDeleteTask"
            />
        </VueDraggable>
    </div>
</template>
