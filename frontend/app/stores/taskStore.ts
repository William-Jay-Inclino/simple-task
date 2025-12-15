import { defineStore } from "pinia";
import type { Task, TaskDate, TaskDatesResponse, TaskResponse, TasksResponse } from "~/types";
import { useApi } from '~/composables/useApi'
import { useDebounceFn } from '@vueuse/core'


export const useTaskStore = defineStore('task', () => {
    const { apiFetch, getCsrfCookie } = useApi();

    const tasks = ref<Task[]>([]);
    const taskDates = ref<TaskDate[]>([]);
    const selectedDate = ref('today');
    const searchQuery = ref('');
    const isLoading = ref(false);
    const error = ref<string | null>(null);

    onMounted(async () => {
        searchQuery.value = ''
        await getCsrfCookie()
        await handleDateSelect(selectedDate.value)
    })

    watch(() => searchQuery.value, (newQuery) => {
        debouncedSearchFn(newQuery)
    })

    const filteredTasks = computed((): Task[] => {
        return tasks.value;
    });

    async function fetchTasks(payload: { date?: string; isCompleted?: boolean; search?: string } = {}): Promise<Task[]> {
        isLoading.value = true;
        error.value = null;
        
        try {
            const params = new URLSearchParams();
            
            if (payload.date) {
                params.append('date', payload.date);
            }
            
            if (payload.isCompleted !== undefined) {
                params.append('is_completed', payload.isCompleted ? '1' : '0');
            }

            if (payload.search) {
                params.append('search', payload.search);
            }
            
            const queryString = params.toString();
            const url = queryString ? `/tasks?${queryString}` : '/tasks';
            
            const response = await apiFetch<TasksResponse>(url, {
                method: 'GET',
            });

            console.log('response', response);
            
            return response.data;
        } catch (err: any) {
            error.value = err.response?.data?.message || 'Failed to fetch tasks';
            console.error('Error fetching tasks:', err);
            throw err;
        } finally {
            isLoading.value = false;
        }
    }

    function setTasks(payload: { tasks: Task[] }) {
        tasks.value = payload.tasks;
    }

    async function addTask(statement: string) {
        if (!statement.trim()) {
            throw new Error('Task statement cannot be empty');
        }
        
        isLoading.value = true;
        error.value = null;
        
        try {
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

            const response = await apiFetch<TaskResponse>('/tasks', {
                method: 'POST',
                data: {
                    statement: statement.trim(),
                    task_date: taskDate,
                    is_completed: false,
                },
            });
            
            tasks.value.unshift(response.data);
            return response.data;
        } catch (err: any) {
            error.value = err.response?.data?.message || 'Failed to create task';
            console.error('Error creating task:', err);
            throw err;
        } finally {
            isLoading.value = false;
        }
    }

    async function editTask(taskId: number, statement: string) {
        if (!statement.trim()) {
            throw new Error('Task statement cannot be empty');
        }
        
        isLoading.value = true;
        error.value = null;
        
        try {
            const response = await apiFetch<TaskResponse>(`/tasks/${taskId}`, {
                method: 'PUT',
                data: {
                    statement: statement.trim(),
                },
            });
            
            const index = tasks.value.findIndex(t => t.id === taskId);
            if (index !== -1) {
                tasks.value[index] = response.data;
            }
            
            return response.data;
        } catch (err: any) {
            error.value = err.response?.data?.message || 'Failed to update task';
            console.error('Error updating task:', err);
            throw err;
        } finally {
            isLoading.value = false;
        }
    }

    async function deleteTask(taskId: number) {
        isLoading.value = true;
        error.value = null;
        
        try {
            await apiFetch(`/tasks/${taskId}`, {
                method: 'DELETE',
            });
            
            tasks.value = tasks.value.filter(t => t.id !== taskId);
        } catch (err: any) {
            error.value = err.response?.data?.message || 'Failed to delete task';
            console.error('Error deleting task:', err);
            throw err;
        } finally {
            isLoading.value = false;
        }
    }

    async function toggleTaskCompletion(taskId: number) {
        const task = tasks.value.find(t => t.id === taskId);
        if (!task) {
            throw new Error('Task not found');
        }
        
        isLoading.value = true;
        error.value = null;
        
        try {
            const response = await apiFetch<TaskResponse>(`/tasks/${taskId}`, {
                method: 'PUT',
                data: {
                    is_completed: !task.is_completed,
                },
            });
            
            const index = tasks.value.findIndex(t => t.id === taskId);
            if (index !== -1) {
                tasks.value[index] = response.data;
            }
            
            return response.data;
        } catch (err: any) {
            error.value = err.response?.data?.message || 'Failed to toggle task completion';
            console.error('Error toggling task completion:', err);
            throw err;
        } finally {
            isLoading.value = false;
        }
    }

    async function fetchTaskDates(limit: number = 30) {
        isLoading.value = true;
        error.value = null;
        
        try {
            const response = await apiFetch<TaskDatesResponse>(`/tasks/dates?limit=${limit}`, {
                method: 'GET',
            });
            
            taskDates.value = response.data;
            return response.data;
        } catch (err: any) {
            error.value = err.response?.data?.message || 'Failed to fetch task dates';
            console.error('Error fetching task dates:', err);
            throw err;
        } finally {
            isLoading.value = false;
        }
    }

    async function reorderTasks(taskIds: number[], date: string) {
        isLoading.value = true;
        error.value = null;
        
        try {
            await apiFetch('/tasks/reorder', {
                method: 'POST',
                data: {
                    date: date,
                    task_ids: taskIds,
                },
            });
        } catch (err: any) {
            error.value = err.response?.data?.message || 'Failed to reorder tasks';
            console.error('Error reordering tasks:', err);
            throw err;
        } finally {
            isLoading.value = false;
        }
    }

    async function debouncedSearch(query: string) {
        try {
            if (query.trim()) {
                // Search across all dates
                const tasks = await fetchTasks({ search: query.trim() })
                setTasks({ tasks })
            } else {
                await handleDateSelect(selectedDate.value)
            }
        } catch (error) {
            console.error('Failed to search tasks:', error)
        }
    }

    async function handleDateSelect(date: string) {
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
            
            const tasks = await fetchTasks({ date: dateFilter })
            setTasks({ tasks })
        } catch (error) {
            console.error('Failed to fetch tasks:', error)
        }
    }

    const debouncedSearchFn = useDebounceFn(debouncedSearch, 300)

    return {
        tasks,
        taskDates,
        selectedDate,
        searchQuery,
        filteredTasks,
        isLoading,
        error,
        fetchTasks,
        fetchTaskDates,
        setTasks,
        addTask,
        editTask,
        deleteTask,
        toggleTaskCompletion,
        reorderTasks,
        handleDateSelect,
    };
});