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

    #[Route('/todo/create', name: 'app_todo_create', methods: ['GET', 'POST'])]
    public function createTask(Request $request): Response
    {
        $user = $this->getUser();
        
        if (!$user) {
            throw $this->createAccessDeniedException('User is not authenticated.');
        }
        $accessToken = $user->getTodoAccessToken();
        if ($request->isMethod('POST')) {           
            $content = $request->request->get('content');
            $due = $request->request->get('due');
            $description = $request->request->get('description');
            $priority = $request->request->getInt('priority', 1); 
            $task = $this->todoService->createTask($accessToken, $content, $due, $description, $priority);
            return $this->redirectToRoute('app_todo');
        }
        return $this->render('todo/create_task.html.twig');
    }
    
}
