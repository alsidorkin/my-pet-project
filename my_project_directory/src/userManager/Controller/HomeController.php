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
    
            return $this->render('home/index.html.twig', [
                'tasks' => $tasks,
            ]);
        }
    }
