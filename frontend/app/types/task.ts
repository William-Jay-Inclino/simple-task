export interface Task {
    id: number
    user_id: number
    statement: string
    task_date: string
    order: number
    is_completed: boolean
    created_at: string
    updated_at: string
}

export interface TasksResponse {
    data: Task[];
}

export interface TaskResponse {
    success: boolean;
    message: string;
    data?: Task;
}

export interface TaskDate {
    date: string;
    task_count: number;
}

export interface TaskDatesResponse {
    data: TaskDate[];
}