import { defineStore } from "pinia";
import type { Task, TaskDate } from "~/types";
import { useDebounceFn } from '@vueuse/core'
import { showSuccessToast, showErrorToast } from "~/utils";
import * as taskService from "~/services/task.api";

export const useTaskStore = defineStore('task', () => {

    const tasks = ref<Task[]>([]);
    const taskDates = ref<TaskDate[]>([]);
    const selectedDate = ref('today');
    const searchQuery = ref('');

    // handler for the search action on TaskHeader.vue
    watch(() => searchQuery.value, (newQuery) => {
        debouncedSearchTask(newQuery)
    })

    function setTasks(payload: { tasks: Task[] }) {
        tasks.value = payload.tasks;
    }

    function setTaskDates(payload: { taskDates: TaskDate[] }) {
        taskDates.value = payload.taskDates;
    }

    // fetchTasks based on searchQuery
    async function searchTasks(query: string) {
        try {
            if (query.trim()) {
                // Search across all dates
                const tasks = await taskService.fetchTasks({ search: query.trim() })
                setTasks({ tasks })
            } else {
                await setDateNavigation(selectedDate.value)
            }
        } catch (error) {
            console.error('Failed to search tasks:', error)
        }
    }

    async function addTask(statement: string) {

        try {
            if (!statement.trim()) {
                throw new Error('Task statement cannot be empty');
            }
            
            // Convert selectedDate to YYYY-MM-DD format
            let taskDate: string;
            if (selectedDate.value === 'today') {
                const today = new Date();
                taskDate = today.toISOString().split('T')[0] ?? '';
            } else if (selectedDate.value === 'yesterday') {
                const yesterday = new Date();
                yesterday.setDate(yesterday.getDate() - 1);
                taskDate = yesterday.toISOString().split('T')[0] ?? '';
            } else {
                taskDate = selectedDate.value;
            }
    
            const response = await taskService.createTask({ statement: statement.trim(), taskDate });
    
            if(response.success && response.data) {
                tasks.value.unshift(response.data);
                showSuccessToast(response.message || 'Task added successfully');
            } else {
                showErrorToast(response.message || 'Failed to add task');
            }

        } catch (err: any) {
            console.error('Error adding task:', err);
            throw err;
        }
    }

    async function editTask(taskId: number, statement: string) {
        
        try {
            if (!statement.trim()) {
                throw new Error('Task statement cannot be empty');
            }
            const response = await taskService.updateTask({ taskId, statement: statement.trim() });

            if(response.success && response.data) {
                showSuccessToast(response.message || 'Task updated successfully');
                const index = tasks.value.findIndex(t => t.id === taskId);
                if (index !== -1) {
                    tasks.value[index] = response.data;
                }
            } else {
                showErrorToast(response.message || 'Failed to update task');
            }
            
        } catch (err: any) {
            console.error('Error updating task:', err);
            throw err;
        } 
    }

    async function removeTask(taskId: number) {
        
        try {
            const response = await taskService.deleteTask(taskId);

            if(response.success === true) {
                showSuccessToast(response.message || 'Task deleted successfully');
                tasks.value = tasks.value.filter(t => t.id !== taskId);
            } else {
                showErrorToast(response.message || 'Failed to delete task');
            }
            
        } catch (err: any) {
            console.error('Error deleting task:', err);
            throw err;
        }
    }

    async function toggleTaskCompletion(taskId: number) {
        
        try {
            const task = tasks.value.find(t => t.id === taskId);
            if (!task) {
                throw new Error('Task not found');
            }
            const response = await taskService.updateTask({ taskId, isCompleted: !task.is_completed });
            
            if(response.success && response.data) {
                const index = tasks.value.findIndex(t => t.id === taskId);
                if (index !== -1) {
                    tasks.value[index] = response.data;
                }
            } else {
                showErrorToast(response.message || 'Failed to toggle task completion');
            }

        } catch (err: any) {
            console.error('Error toggling task completion:', err);
            throw err;
        }
    }

    async function reorderTasks(taskIds: number[], date: string) {
        try {
            await taskService.reorderTasks(taskIds, date);
        } catch (err: any) {
            console.error('Error reordering tasks:', err);
            throw err;
        } 
    }

    // handler for date selection on TaskSideBar.vue
    async function setDateNavigation(date: string) {
        selectedDate.value = date
        try {
            let dateFilter: string | undefined
            
            if (date === 'today') {
                const today = new Date()
                dateFilter = formatDate(today)
            } else if (date === 'yesterday') {
                const yesterday = new Date()
                yesterday.setDate(yesterday.getDate() - 1)
                dateFilter = formatDate(yesterday)
            } else {
                // For specific dates (YYYY-MM-DD format)
                dateFilter = date
            }
            
            const tasks = await taskService.fetchTasks({ date: dateFilter })
            setTasks({ tasks })
        } catch (error) {
            console.error('Failed to fetch tasks:', error)
        }
    }

    async function $reset() {
        tasks.value = [];
        taskDates.value = [];
        selectedDate.value = 'today';
        searchQuery.value = '';
    }

    const debouncedSearchTask = useDebounceFn(searchTasks, 300)

    return {
        tasks,
        taskDates,
        selectedDate,
        searchQuery,
        setTasks,
        setTaskDates,
        addTask,
        editTask,
        removeTask,
        toggleTaskCompletion,
        reorderTasks,
        setDateNavigation,
        $reset,
    };
});