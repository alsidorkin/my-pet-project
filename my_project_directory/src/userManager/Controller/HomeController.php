<?php
namespace App\userManager\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\TodoService;
use App\Service\WeatherService;
use GuzzleHttp\Client;

class HomeController extends AbstractController
{
    private $todoService;
    private $weatherService;

    public function __construct(TodoService $todoService, WeatherService $weatherService) 
    {
        $this->todoService = $todoService;
        $this->weatherService = $weatherService;
    }

    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException('User is not authenticated.');
        }
        $accessToken = $user->getTodoAccessToken();
        $tasks = $this->todoService->getTasks($accessToken);

        $holidays = $this->fetchHolidays('UA', date('Y'));

        $lat = 50.45; 
        $lon = 30.523;
        $cnt = 16; 
        $forecast = $this->weatherService->getDailyForecast($lat, $lon, $cnt);

        return $this->render('home/index.html.twig', [
            'tasks' => $tasks,
            'holidays' => $holidays,
            'forecast' => $forecast,
        ]);
    }

    private function fetchHolidays(string $country, int $year): array
    {
        $api_key = $_ENV['CALENDARIFIC_API_KEY']; 
        $client = new Client();
        $response = $client->request('GET', "https://calendarific.com/api/v2/holidays", [
            'query' => [
                'api_key' => $api_key,
                'country' => $country,
                'year' => $year,
            ]
        ]);

        $holidays = json_decode($response->getBody()->getContents(), true);

        return $holidays['response']['holidays'];
    }
}

