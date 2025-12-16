<script setup lang="ts">
import { useTaskStore } from '~/stores/taskStore'
import { formatDateLabel, getWeekKey, formatDate } from '~/utils/date'
import { LucideX } from 'lucide-vue-next'

interface DateItem {
    label: string
    value: string
    section?: string
    count?: number
}

interface Props {
    selectedDate?: string
    isOpen?: boolean
}

const props = withDefaults(defineProps<Props>(), {
    selectedDate: 'today',
    isOpen: false
})

const emit = defineEmits<{
    selectDate: [value: string]
    close: []
}>()

const taskStore = useTaskStore()
const { taskDates } = storeToRefs(taskStore)

const dateItems = computed((): DateItem[] => {
    const items: DateItem[] = []
    const today = new Date()
    const yesterday = new Date(today)
    yesterday.setDate(yesterday.getDate() - 1)

    const todayStr = formatDate(today)
    const yesterdayStr = formatDate(yesterday)

    // Always show Today and Yesterday
    items.push({ label: 'Today', value: 'today' })
    items.push({ label: 'Yesterday', value: 'yesterday' })

    if (!taskDates.value.length) {
        return items
    }

    // Group dates by week
    const dateGroups: { [key: string]: { date: string; count: number }[] } = {}
    
    taskDates.value.forEach((taskDate) => {
        const date = new Date(taskDate.date)
        const dateStr = formatDate(date)
        
        // Skip if it's today or yesterday (already shown)
        if (dateStr === todayStr || dateStr === yesterdayStr) {
            return
        }

        const weekKey = getWeekKey(date)
        if (!dateGroups[weekKey]) {
            dateGroups[weekKey] = []
        }
        
        dateGroups[weekKey].push({
            date: taskDate.date,
            count: taskDate.task_count
        })
    })

    // Add grouped dates
    Object.entries(dateGroups).forEach(([weekKey, dates]) => {
        items.push({
            label: weekKey,
            value: '',
            section: 'header'
        })
        
        dates.forEach((item) => {
            const date = new Date(item.date)
            items.push({
                label: formatDateLabel(date),
                value: item.date,
                count: item.count
            })
        })
    })

    return items
})

const handleDateSelect = async(value: string) => {
    emit('selectDate', value)
    emit('close') // Close sidebar on mobile after selection
}

const isSelected = (value: string) => {
    return props.selectedDate === value
}

// Fetch task dates on mount
onMounted(async () => {
    try {
        await taskStore.fetchTaskDates()
    } catch (error) {
        console.error('Failed to fetch task dates:', error)
    }
})
</script>

<template>
    <!-- Mobile Drawer -->
    <Transition
        enter-active-class="transition-transform duration-300"
        leave-active-class="transition-transform duration-300"
        enter-from-class="-translate-x-full"
        leave-to-class="-translate-x-full"
    >
        <aside
            v-if="isOpen"
            class="fixed inset-y-0 left-0 z-40 w-64 overflow-y-auto bg-white p-6 shadow-xl lg:hidden"
        >
            <!-- Close Button -->
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Dates</h2>
                <button
                    @click="emit('close')"
                    class="rounded-lg p-1.5 text-gray-500 hover:bg-gray-100 transition-colors"
                    aria-label="Close sidebar"
                >
                    <LucideX :size="20" />
                </button>
            </div>
            
            <div class="space-y-1">
                <template v-for="item in dateItems" :key="item.value || item.label">
                    <div v-if="item.section === 'header'" class="pt-4">
                        <p class="px-4 text-xs text-gray-400">{{ item.label }}</p>
                    </div>
                    <button
                        v-else
                        @click="handleDateSelect(item.value)"
                        class="w-full rounded-lg px-4 py-2 text-left text-sm flex items-center justify-between"
                        :class="isSelected(item.value) 
                            ? 'rounded-full bg-black font-medium text-white' 
                            : 'hover:bg-gray-100'"
                    >
                        <span>{{ item.label }}</span>
                    </button>
                </template>
            </div>
        </aside>
    </Transition>

    <!-- Desktop Sidebar -->
    <aside class="hidden lg:block w-64 overflow-y-auto bg-white p-6 border-r border-gray-200">
        <div class="space-y-1">
            <template v-for="item in dateItems" :key="item.value || item.label">
                <div v-if="item.section === 'header'" class="pt-4">
                    <p class="px-4 text-xs text-gray-400">{{ item.label }}</p>
                </div>
                <button
                    v-else
                    @click="handleDateSelect(item.value)"
                    class="w-full rounded-lg px-4 py-2 text-left text-sm flex items-center justify-between"
                    :class="isSelected(item.value) 
                        ? 'rounded-full bg-black font-medium text-white' 
                        : 'hover:bg-gray-100'"
                >
                    <span>{{ item.label }}</span>
                </button>
            </template>
        </div>
    </aside>
</template>
