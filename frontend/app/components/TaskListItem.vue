<script setup lang="ts">
import { LucideTrash2, LucideCheck, LucideGripVertical } from 'lucide-vue-next'
import type { Task } from '~/types'
import { useTaskStore } from '~/stores/taskStore'

interface Props {
    task: Task
}

const props = defineProps<Props>()

const emit = defineEmits<{
    toggle: [taskId: number]
    delete: [taskId: number]
}>()

const taskStore = useTaskStore()
const isEditing = ref(false)
const editedStatement = ref('')
const inputRef = ref<HTMLInputElement | null>(null)

const isDragDisabled = computed(() => !!taskStore.searchQuery.trim())

const handleToggle = () => {
    emit('toggle', props.task.id)
}

const handleDelete = () => {
    emit('delete', props.task.id)
}

const startEditing = () => {
    if (props.task.is_completed) return
    isEditing.value = true
    editedStatement.value = props.task.statement
    nextTick(() => {
        inputRef.value?.focus()
        inputRef.value?.select()
    })
}

const saveEdit = async () => {
    if (!editedStatement.value.trim() || editedStatement.value === props.task.statement) {
        cancelEdit()
        return
    }

    try {
        await taskStore.editTask(props.task.id, editedStatement.value.trim())
        isEditing.value = false
    } catch (error) {
        console.error('Failed to update task:', error)
    }
}

const cancelEdit = () => {
    isEditing.value = false
    editedStatement.value = ''
}

const handleKeydown = (e: KeyboardEvent) => {
    if (e.key === 'Enter') {
        e.preventDefault()
        saveEdit()
    } else if (e.key === 'Escape') {
        cancelEdit()
    }
}
</script>

<template>
    <div class="group flex items-center gap-3 rounded-xl border border-gray-200 bg-white p-4 hover:shadow-sm transition-shadow">
        <!-- Drag Handle -->
        <div 
            class="drag-handle text-gray-400 hover:text-gray-600 transition-colors"
            :class="isDragDisabled ? 'cursor-not-allowed opacity-50' : 'cursor-move'"
        >
            <LucideGripVertical :size="20" />
        </div>

        <div class="relative flex items-center">
            <input
                type="checkbox"
                :checked="task.is_completed"
                @change="handleToggle"
                class="h-5 w-5 cursor-pointer appearance-none rounded-full border-2 border-gray-300 checked:bg-black checked:border-black transition-colors"
            />
            <svg
                v-if="task.is_completed"
                class="absolute left-0 h-5 w-5 p-0.5 pointer-events-none text-white"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7" />
            </svg>
        </div>

        <!-- Edit Mode -->
        <div v-if="isEditing" class="flex flex-1 items-center gap-2">
            <input
                ref="inputRef"
                v-model="editedStatement"
                type="text"
                @keydown="handleKeydown"
                class="flex-1 rounded-lg border border-gray-300 px-3 py-1.5 text-sm focus:border-gray-400 focus:outline-none focus:ring-1 focus:ring-gray-400"
            />
            <button
                @click="saveEdit"
                class="flex h-8 w-8 items-center justify-center rounded-lg bg-black text-white hover:bg-gray-800 transition-colors"
                title="Save (Enter)"
            >
                <LucideCheck :size="16" />
            </button>
        </div>

        <!-- View Mode -->
        <p
            v-else
            @click="startEditing"
            class="flex-1 text-sm transition-colors"
            :class="{ 
                'line-through text-gray-500': task.is_completed,
                'cursor-pointer hover:text-gray-700': !task.is_completed,
                'cursor-not-allowed': task.is_completed
            }"
        >
            {{ task.statement }}
        </p>

        <button
            v-if="!isEditing"
            @click="handleDelete"
            class="opacity-0 group-hover:opacity-100 text-gray-400 hover:text-red-500 transition-all"
        >
            <LucideTrash2 :size="18" />
        </button>
    </div>
</template>
