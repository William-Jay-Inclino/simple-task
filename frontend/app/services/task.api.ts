import type { Task, TaskDate, TaskDatesResponse, TaskResponse, TasksResponse } from "~/types";
import { apiFetch } from '~/utils/api'

export async function fetchTasks(payload: { date?: string; search?: string } = {}): Promise<Task[]> {
    const response = await apiFetch<TasksResponse>('/tasks', {
        method: 'GET',
        params: {
            date: payload.date,
            search: payload.search
        }
    });

    return response.data;
}

export async function fetchTaskDates(limit: number = 30): Promise<TaskDate[]> {
    const response = await apiFetch<TaskDatesResponse>('/tasks/dates', {
        method: 'GET',
        params: { limit }
    });
    
    return response.data;
}

export async function createTask(payload: {
    statement: string,
    taskDate: string
}) {
    return await apiFetch<TaskResponse>('/tasks', {
        method: 'POST',
        data: {
            statement: payload.statement.trim(),
            task_date: payload.taskDate,
            is_completed: false,
        },
    });
}

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

export async function deleteTask(taskId: number) {
    return await apiFetch<TaskResponse>(`/tasks/${taskId}`, {
        method: 'DELETE',
    });
}

export async function reorderTasks(taskIds: number[], date: string) {
    return await apiFetch('/tasks/reorder', {
        method: 'POST',
        data: {
            date: date,
            task_ids: taskIds,
        },
    });
}