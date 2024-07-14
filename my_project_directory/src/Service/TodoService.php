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

}
