<script setup lang="ts">
import { LucideArrowUp } from 'lucide-vue-next'
import { useTaskStore } from '~/stores/taskStore'

interface Props {
    placeholder?: string
    position?: 'top' | 'bottom'
    rows?: number
}

const props = withDefaults(defineProps<Props>(), {
    placeholder: 'What else do you need to do?',
    position: 'bottom',
    rows: 3
})

const taskStore = useTaskStore()
const newTaskStatement = ref('')

const handleAddTask = () => {
    if (newTaskStatement.value.trim()) {
        taskStore.addTask(newTaskStatement.value.trim())
        newTaskStatement.value = ''
    }
}

const handleKeyDown = (event: KeyboardEvent) => {
    if (event.key === 'Enter' && (event.metaKey || event.ctrlKey)) {
        event.preventDefault()
        handleAddTask()
    }
}
</script>

<template>
    <div
        :class="[
            position === 'bottom' 
                ? 'fixed bottom-0 left-64 right-0 bg-white p-6' 
                : 'w-full'
        ]"
    >
        <div :class="position === 'bottom' ? 'mx-auto max-w-3xl' : ''">
            <div class="relative">
                <textarea
                    v-model="newTaskStatement"
                    :placeholder="placeholder"
                    class="w-full rounded-2xl border border-gray-200 bg-white p-4 pr-12 text-sm focus:border-gray-300 focus:outline-none resize-none"
                    :rows="rows"
                    @keydown="handleKeyDown"
                ></textarea>
                <button
                    @click="handleAddTask"
                    class="absolute bottom-4 right-4 rounded-full bg-black p-2 text-white hover:bg-gray-800 disabled:opacity-50 disabled:cursor-not-allowed"
                    :disabled="!newTaskStatement.trim()"
                >
                    <LucideArrowUp :size="20" />
                </button>
            </div>
        </div>
    </div>
</template>
