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

    
}
