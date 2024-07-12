<?php

namespace App\Service;

use GuzzleHttp\Client;
use App\Exception\NewsApiException;

class NewsApiService
{
    private $apiKey;
    private $client;

    public function __construct(string $apiKey)
    {
        $this->apiKey = $_ENV['NEWS_API_KEY'];
        $this->client = new Client([
            'base_uri' => 'https://newsapi.org/v2/',
            'timeout'  => 30,
        ]);
    }

    public function getTopHeadlines($country = null, $category = null, $pageSize = null, $page = null)
    {
        $payload = [];

        if (!is_null($country)) {
            $payload['country'] = $country;
        }

        if (!is_null($category)) {
            $payload['category'] = $category;
        }

        if (!is_null($pageSize)) {
            $payload['pageSize'] = $pageSize;
        }

        if (!is_null($page)) {
            $payload['page'] = $page;
        }

        try {
           
            $response = $this->client->request('GET', 'top-headlines', [
                'query' => $payload,
                'headers' => [
                    'Authorization' => "Bearer {$this->apiKey}",
                ],
            ]);

            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody()->__toString(), true);
            } else {
                throw new NewsApiException("Failed to fetch top headlines");
            }
        } catch (\Exception $e) {
            throw new NewsApiException($e->getMessage());
        }
    }
}