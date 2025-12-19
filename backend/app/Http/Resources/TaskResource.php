<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'statement' => $this->statement,
            'is_completed' => $this->is_completed,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }

    public function withResponse($request, $response): void
    {
        $original = json_decode($response->getContent(), true);
        $method = $request->method();
        
        $messages = [
            'POST' => 'Task created successfully',
            'GET' => 'Task retrieved successfully',
            'PUT' => 'Task updated successfully',
            'PATCH' => 'Task updated successfully',
            'DELETE' => 'Task deleted successfully',
        ];

        if ($method === 'DELETE') {
            $responseData = [
                'success' => true,
                'message' => $messages[$method],
                'data' => null,
            ];
        } else {
            // Extract data from Laravel's default wrapper
            $data = $original['data'] ?? $original;
            
            $responseData = [
                'success' => true,
                'message' => $messages[$method] ?? 'Operation completed successfully',
                'data' => $data,
            ];
        }

        $response->setContent(json_encode($responseData));
    }


}
