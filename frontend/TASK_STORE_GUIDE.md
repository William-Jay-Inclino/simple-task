# Task Store - Frontend Implementation Guide

## Overview
The task store is now fully implemented using Pinia with all CRUD operations connected to the Laravel Sanctum backend.

## Features

### State Management
- `tasks`: Array of all tasks
- `searchQuery`: Client-side search filter
- `filteredTasks`: Computed property for filtered results
- `isLoading`: Loading state for async operations
- `error`: Error message from last operation

### API Methods

#### 1. Fetch Tasks
```typescript
// Fetch all tasks
await taskStore.fetchTasks()

// Fetch tasks for a specific date
await taskStore.fetchTasks({ date: '2025-12-15' })

// Fetch only completed tasks
await taskStore.fetchTasks({ isCompleted: true })

// Fetch pending tasks for a date
await taskStore.fetchTasks({ 
  date: '2025-12-15', 
  isCompleted: false 
})
```

#### 2. Create Task
```typescript
const newTask = await taskStore.addTask('Buy groceries')
// Returns the created task with server-generated ID
```

#### 3. Update Task
```typescript
// Update task statement
await taskStore.editTask(taskId, 'Updated task description')
```

#### 4. Toggle Completion
```typescript
// Toggle is_completed status
await taskStore.toggleTaskCompletion(taskId)
```

#### 5. Delete Task
```typescript
await taskStore.deleteTask(taskId)
```

### Client-Side Search
```typescript
// Set search query for local filtering
taskStore.searchQuery = 'groceries'

// Access filtered results
const results = taskStore.filteredTasks
```

## Usage in Components

### Setup Store
```vue
<script setup lang="ts">
import { useTaskStore } from '~/stores/taskStore'

const taskStore = useTaskStore()
const { tasks, filteredTasks, isLoading, error } = storeToRefs(taskStore)

// Fetch tasks on mount
onMounted(async () => {
  await taskStore.fetchTasks()
})
</script>
```

### Display Tasks
```vue
<template>
  <div>
    <div v-if="isLoading">Loading...</div>
    <div v-if="error" class="error">{{ error }}</div>
    
    <div v-for="task in filteredTasks" :key="task.id">
      <p>{{ task.statement }}</p>
      <button @click="taskStore.toggleTaskCompletion(task.id)">
        {{ task.is_completed ? 'Mark Incomplete' : 'Mark Complete' }}
      </button>
      <button @click="taskStore.deleteTask(task.id)">Delete</button>
    </div>
  </div>
</template>
```

### Create Task Form
```vue
<script setup lang="ts">
const newTaskStatement = ref('')
const taskStore = useTaskStore()

async function handleSubmit() {
  try {
    await taskStore.addTask(newTaskStatement.value)
    newTaskStatement.value = ''
  } catch (err) {
    console.error('Failed to create task:', err)
  }
}
</script>

<template>
  <form @submit.prevent="handleSubmit">
    <input v-model="newTaskStatement" placeholder="Enter task..." />
    <button type="submit" :disabled="!newTaskStatement.trim()">Add Task</button>
  </form>
</template>
```

### Search Tasks
```vue
<template>
  <input 
    v-model="taskStore.searchQuery" 
    placeholder="Search tasks..." 
  />
  
  <!-- filteredTasks will automatically update -->
  <div v-for="task in filteredTasks" :key="task.id">
    {{ task.statement }}
  </div>
</template>
```

## Error Handling

All async methods throw errors that should be caught:

```typescript
try {
  await taskStore.addTask('New task')
} catch (error) {
  // Error is already logged and stored in taskStore.error
  // Handle UI feedback here
  alert('Failed to create task')
}
```

## Best Practices

1. **Loading States**: Use `isLoading` to show spinners/loaders
2. **Error Display**: Show `error` to users when operations fail
3. **Optimistic Updates**: Tasks are updated locally immediately for better UX
4. **Server Sync**: Always use the server response to update local state
5. **Validation**: Empty statements are rejected before API calls

## Backend Routes

All endpoints require authentication via Laravel Sanctum:

- `GET /api/tasks` - List tasks (with filters)
- `POST /api/tasks` - Create task
- `GET /api/tasks/{id}` - Show task
- `PUT /api/tasks/{id}` - Update task
- `DELETE /api/tasks/{id}` - Delete task

## Authentication Flow

Before using the task store, ensure the user is authenticated:

```typescript
import { useApi } from '~/composables/useApi'

const { getCsrfCookie } = useApi()

// Get CSRF cookie before making authenticated requests
await getCsrfCookie()

// Then proceed with task operations
await taskStore.fetchTasks()
```

## Type Safety

All responses are properly typed with TypeScript:

```typescript
interface Task {
  id: number
  user_id: number
  statement: string
  is_completed: boolean
  created_at: string
  updated_at: string
}
```
