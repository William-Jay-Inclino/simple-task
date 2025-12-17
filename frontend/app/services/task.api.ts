import type { Task, TaskDate, TaskDatesResponse, TaskResponse, TasksResponse } from "~/types";
// Import the utility you created
import { apiFetch } from '~/utils/api'

/**
 * Fetch all tasks with optional filters
 */
export async function fetchTasks(payload: { date?: string; search?: string } = {}): Promise<Task[]> {
    const response = await apiFetch<TasksResponse>('/tasks', {
        method: 'GET',
        // Axios handles URLSearchParams automatically via the params key
        params: {
            date: payload.date,
            search: payload.search
        }
    });

    return response.data;
}

/**
 * Fetch unique dates that have tasks
 */
export async function fetchTaskDates(limit: number = 30): Promise<TaskDate[]> {
    const response = await apiFetch<TaskDatesResponse>('/tasks/dates', {
        method: 'GET',
        params: { limit }
    });
    
    return response.data;
}

/**
 * Create a new task
 */
export async function createTask(payload: {
    statement: string,
    taskDate: string
}) {
    // Note: data is the body of the request
    return await apiFetch<TaskResponse>('/tasks', {
        method: 'POST',
        data: {
            statement: payload.statement.trim(),
            task_date: payload.taskDate,
            is_completed: false,
        },
    });
}

/**
 * Update an existing task (Statement or Completion status)
 */
export async function updateTask(payload: {
    taskId: number, 
    statement?: string,
    isCompleted?: boolean
}) {
    return await apiFetch<TaskResponse>(`/tasks/${payload.taskId}`, {
        method: 'PUT',
        data: {
            statement: payload.statement?.trim(),
            is_completed: payload.isCompleted,
        },
    });
}

/**
 * Delete a task
 */
export async function deleteTask(taskId: number) {
    return await apiFetch<TaskResponse>(`/tasks/${taskId}`, {
        method: 'DELETE',
    });
}

/**
 * Reorder tasks for a specific date
 */
export async function reorderTasks(taskIds: number[], date: string) {
    return await apiFetch('/tasks/reorder', {
        method: 'POST',
        data: {
            date: date,
            task_ids: taskIds,
        },
    });
}