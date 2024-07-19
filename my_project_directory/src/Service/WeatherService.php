<?php
namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class WeatherService
{
    private $client;
    private $apiKey;

    public function __construct(HttpClientInterface $client, string $apiKey)
    {
        $this->client = $client;
        $this->apiKey = $_ENV['OPENWEATHERMAP_API_KEY'];
    }

    public function getWeather(string $city): array
    {
        $response = $this->client->request(
            'GET',
            "https://api.openweathermap.org/data/2.5/weather?q={$city}&appid={$this->apiKey}&units=metric"
        );

        return $response->toArray();
    }

    public function getDailyForecast(float $lat, float $lon, int $cnt): array
    {
        $response = $this->client->request(
            'GET',
            "https://api.openweathermap.org/data/2.5/forecast?lat={$lat}&lon={$lon}&appid={$this->apiKey}"
        );

        return $response->toArray();
    }

   

    public function getDataWeather(float $lat, float $lon,$date): array
    {
        $response = $this->client->request(
            'GET',
            "https://api.openweathermap.org/data/3.0/onecall/day_summary?lat={$lat}&lon={$lon}&date={$date}&appid={$this->apiKey}"
        );

        return $response->toArray();
    }

}
