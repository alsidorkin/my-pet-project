<?php

namespace App\Service;

use GuzzleHttp\Client;
use Psr\Log\LoggerInterface;

class TodoService
{
    private $client;
    
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function getTasks(string $accessToken): array
    {
        try {
            $response = $this->client->request('GET', 'https://api.todoist.com/rest/v2/tasks', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                ]
            ]);

            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            echo('Error fetching tasks from Todoist API: ' . $e->getMessage());
            return [];
        }
    }

    public function getTask(string $accessToken, string $taskId): array
{
    try {
        $response = $this->client->request('GET', 'https://api.todoist.com/rest/v2/tasks/' . $taskId, [
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
            ]
        ]);

        return json_decode($response->getBody(), true);
        
    } catch (\Exception $e) {
        echo('Error fetching task from Todoist API: ' . $e->getMessage());
        return [];
    }
}
    public function createTask(string $accessToken, string $content, string $due, ?string $description = null, int $priority = 1 )
    {
        
        $url = 'https://api.todoist.com/rest/v2/tasks';
        $headers = [
            'Authorization' => 'Bearer ' . $accessToken,
            'Content-Type' => 'application/json',
        ];
        $data = [
            'content' => $content,
            'priority' => $priority,
            'due_string' => $due,
        ];
        
        if ($description !== null) {
            $data['description'] = $description;
        }
       
        $response = $this->client->request('POST', $url, [
            'headers' => $headers,
            'json' => $data,
        ]);

      
        $statusCode = $response->getStatusCode();
        $responseData = $response->getBody()->getContents();

    
        return $responseData;
    }

    public function updateTask(string $accessToken, string $taskId, string $content, ?string $due, ?string $description, int $priority): bool
    {
        try {
            $params = [
                'content' => $content,
                'priority' => $priority
            ];
    
            if ($due) {
                $params['due_string'] = $due;
            }
    
            if ($description) {
                $params['description'] = $description;
            }
    
            $response = $this->client->request('POST', "https://api.todoist.com/rest/v2/tasks/{$taskId}", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json'
                ],
                'json' => $params,
            ]);
    
            return $response->getStatusCode() === 200;
        } catch (\Exception $e) {
            echo('Error updating task in Todoist API: ' . $e->getMessage());
            return false;
        }
    }

    public function deleteTask(string $accessToken, string $taskId): bool
    {
        try {
            $response = $this->client->request('DELETE', "https://api.todoist.com/rest/v2/tasks/{$taskId}", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json'
                ],
            ]);

            return $response->getStatusCode() === 204;
        } catch (\Exception $e) {
            echo('Error deleting task from Todoist API: ' . $e->getMessage());
            return false;
        }
    }

    public function completeTask(string $accessToken, string $taskId): bool
{
    try {
        $response = $this->client->request('POST', 'https://api.todoist.com/rest/v2/tasks/' . $taskId . '/close', [
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
            ]
        ]);

        return $response->getStatusCode() === 204;
    } catch (\Exception $e) {
        echo('Error marking task as completed: ' . $e->getMessage());
        return false;
    }
}

}
