<?php

namespace App\todo\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\SecurityBundle\Security;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;

#[Route('/profile')]
#[IsGranted('ROLE_USER')]
class TodoAuthController extends AbstractController
{
    private $security;
    private $entityManager;
    private $client;

    public function __construct(Security $security, EntityManagerInterface $entityManager)
    {
        $this->security = $security;
        $this->entityManager = $entityManager;
        $this->client = new Client();
    }

    #[Route('/todo/oauth/start', name: 'todo_oauth_start')]
    public function start(): Response
    {
        $clientId = $_ENV['TODOIST_CLIENT_ID'];
        $redirectUri = $this->generateUrl('todo_oauth_callback', [], 0);

        $url = 'https://todoist.com/oauth/authorize?client_id=' . $clientId . '&scope=data:read_write&state=some_random_state&redirect_uri=' . urlencode($redirectUri);

        return $this->redirect($url);
    }

    #[Route('/callback.php', name: 'todo_oauth_callback')]
    public function callback(Request $request): Response
    {
        $code = $request->query->get('code');
        $state = $request->query->get('state');
     
        if (!$code) {
            return new Response('Authorization failed', 400);
        }

        $clientId = $_ENV['TODOIST_CLIENT_ID'];
        $clientSecret = $_ENV['TODOIST_CLIENT_SECRET'];

        $response = $this->client->request('POST', 'https://todoist.com/oauth/access_token', [
            'form_params' => [
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'code' => $code,
                'redirect_uri' => $this->generateUrl('todo_oauth_callback', [], 0),
            ],
        ]);
        
        $data = json_decode($response->getBody()->getContents(), true);
        
        if (!isset($data['access_token'])) {
            return new Response('No access token found in the response', 400);
        }

        $accessToken = $data['access_token'];
       
        $user = $this->security->getUser();
        
        $user->setTodoAccessToken($accessToken);
      
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_todo');
    }
}
