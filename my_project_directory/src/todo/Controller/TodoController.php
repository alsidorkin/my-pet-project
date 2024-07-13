<?php

namespace App\todo\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Service\TodoService;
use GuzzleHttp\Client;

class TodoController extends AbstractController
{
    private $todoService;

    public function __construct(TodoService $todoService) 
    {
        $this->todoService = $todoService;
    }

    #[Route('/todo', name: 'app_todo')]
    public function index(): Response
    {
        $user = $this->getUser();

        $accessToken = $user->getTodoAccessToken();

        $tasks = $this->todoService->getTasks($accessToken);

        return $this->render('todo/index.html.twig', [
            'tasks' => $tasks,
        ]);
    }
    
}
