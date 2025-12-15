# Task CRUD API Implementation

## Overview
A complete CRUD API implementation for the Task resource following Laravel best practices, Repository Pattern, and comprehensive testing.

## Architecture Components

### 1. Repository Pattern
- **Interface**: `App\Contracts\TaskRepositoryInterface`
- **Implementation**: `App\Repositories\TaskRepository`
- **Registered in**: `App\Providers\AppServiceProvider`

The repository handles all database operations and provides a clean abstraction layer.

### 2. Form Request Validation
- **StoreTaskRequest**: Validates task creation
  - `statement`: required, string, max 5000 characters
  - `is_completed`: optional, boolean
  
- **UpdateTaskRequest**: Validates task updates
  - `statement`: optional, string, max 5000 characters
  - `is_completed`: optional, boolean

### 3. JSON Resources
- **TaskResource**: Transforms Task models into consistent JSON responses
  - Returns: id, user_id, statement, is_completed, created_at, updated_at

### 4. Authorization
- **TaskPolicy**: Enforces ownership-based access control
  - Users can only view, update, and delete their own tasks
  - Automatically discovered by Laravel

### 5. Controller
- **TaskController**: RESTful resource controller
  - `index()`: List user's tasks with filters (search, is_completed, date, pagination)
  - `store()`: Create new task
  - `show()`: View single task
  - `update()`: Update task (statement, is_completed, or both)
  - `destroy()`: Delete task

## API Endpoints

All endpoints require authentication (`auth:sanctum`).

### List Tasks
```
GET /api/tasks
```

**Query Parameters:**
- `search`: Filter by statement text
- `is_completed`: Filter by completion status (0 or 1)
- `date`: Filter by creation date
- `per_page`: Results per page (default: 15)

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "user_id": 1,
      "statement": "Task statement",
      "is_completed": false,
      "created_at": "2025-12-15T00:00:00.000000Z",
      "updated_at": "2025-12-15T00:00:00.000000Z"
    }
  ],
  "links": {...},
  "meta": {...}
}
```

### Create Task
```
POST /api/tasks
```

**Request Body:**
```json
{
  "statement": "New task to complete",
  "is_completed": false
}
```

**Response:** 201 Created with task data

### View Task
```
GET /api/tasks/{id}
```

**Response:** 200 OK with task data

### Update Task
```
PUT /api/tasks/{id}
PATCH /api/tasks/{id}
```

**Request Body (all fields optional):**
```json
{
  "statement": "Updated statement",
  "is_completed": true
}
```

**Response:** 200 OK with updated task data

### Delete Task
```
DELETE /api/tasks/{id}
```

**Response:** 204 No Content

## Testing

Comprehensive test suite with 23 tests covering:
- ✅ Listing tasks with filters
- ✅ Authentication requirements
- ✅ Input validation
- ✅ Authorization (user ownership)
- ✅ CRUD operations
- ✅ Error handling

**Run tests:**
```bash
./vendor/bin/sail artisan test --filter=TaskControllerTest
```

## Code Quality

- ✅ PSR-12 compliant (verified with Laravel Pint)
- ✅ Repository Pattern for testability
- ✅ Form Request validation for input sanitization
- ✅ JSON Resources for consistent API responses
- ✅ Policy-based authorization
- ✅ Comprehensive test coverage

## Files Created/Modified

**New Files:**
- `app/Contracts/TaskRepositoryInterface.php`
- `app/Repositories/TaskRepository.php`
- `app/Http/Requests/StoreTaskRequest.php`
- `app/Http/Requests/UpdateTaskRequest.php`
- `app/Http/Resources/TaskResource.php`
- `app/Policies/TaskPolicy.php`
- `app/Http/Controllers/TaskController.php`
- `database/factories/TaskFactory.php`
- `tests/Feature/TaskControllerTest.php`

**Modified Files:**
- `app/Providers/AppServiceProvider.php` (Repository binding)
- `app/Models/Task.php` (Added HasFactory trait)
- `routes/api.php` (Added task routes)
- `tests/TestCase.php` (Disabled CSRF for tests)

## Usage Example

```php
// List all tasks for authenticated user
$tasks = $this->getJson('/api/tasks');

// Create a new task
$task = $this->postJson('/api/tasks', [
    'statement' => 'Complete project documentation'
]);

// Update only completion status
$updated = $this->putJson("/api/tasks/{$taskId}", [
    'is_completed' => true
]);

// Update only statement
$updated = $this->putJson("/api/tasks/{$taskId}", [
    'statement' => 'Updated task description'
]);

// Delete a task
$this->deleteJson("/api/tasks/{$taskId}");
```
