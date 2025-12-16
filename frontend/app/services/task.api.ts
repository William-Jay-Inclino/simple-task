import type { Task, TaskDate, TaskDatesResponse, TaskResponse, TasksResponse } from "~/types";
import { useApi } from '~/composables/useApi'

export async function fetchTasks(payload: { date?: string; search?: string } = {}): Promise<Task[]> {
    
    const { apiFetch } = useApi();
    const params = new URLSearchParams();
    
    if (payload.date) {
        params.append('date', payload.date);
    }

    if (payload.search) {
        params.append('search', payload.search);
    }
    
    const queryString = params.toString();
    const url = queryString ? `/tasks?${queryString}` : '/tasks';
    
    const response = await apiFetch<TasksResponse>(url, {
        method: 'GET',
    });

    return response.data;
 
}

export async function fetchTaskDates(limit: number = 30): Promise<TaskDate[]> {
    
    const { apiFetch } = useApi();
    const response = await apiFetch<TaskDatesResponse>(`/tasks/dates?limit=${limit}`, {
        method: 'GET',
    });
    
    return response.data;
    
}

export async function createTask(payload: {
    statement: string,
    taskDate: string
}) {
    const { apiFetch } = useApi();
    const { statement, taskDate } = payload;

    const response = await apiFetch<TaskResponse>('/tasks', {
        method: 'POST',
        data: {
            statement: statement.trim(),
            task_date: taskDate,
            is_completed: false,
        },
    });

    return response;

}

export async function updateTask(payload: {
    taskId: number, 
    statement?: string,
    isCompleted?: boolean
}) {
    
    const { apiFetch } = useApi();
    const { taskId, statement, isCompleted } = payload;

    const response = await apiFetch<TaskResponse>(`/tasks/${taskId}`, {
        method: 'PUT',
        data: {
            statement: statement?.trim(),
            is_completed: isCompleted,
        },
    });

    return response;
   
}

export async function deleteTask(taskId: number) {
    const { apiFetch } = useApi();

    const response = await apiFetch<TaskResponse>(`/tasks/${taskId}`, {
        method: 'DELETE',
    });

    return response;
}

export async function reorderTasks(taskIds: number[], date: string) {
    const { apiFetch } = useApi();
    await apiFetch('/tasks/reorder', {
        method: 'POST',
        data: {
            date: date,
            task_ids: taskIds,
        },
    });
}