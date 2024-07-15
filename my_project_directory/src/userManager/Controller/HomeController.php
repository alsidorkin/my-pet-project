<?php
namespace App\userManager\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\TodoService;
use GuzzleHttp\Client;

class HomeController extends AbstractController
{
    private $todoService;

    public function __construct(TodoService $todoService) 
    {
        $this->todoService = $todoService;
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

        return $this->render('home/index.html.twig', [
            'tasks' => $tasks,
            'holidays' => $holidays,
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

