<?php
namespace App\news\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\NewsApiService;

class NewsController extends AbstractController
{
    private $newsApiService;
   
    public function __construct(NewsApiService $newsApiService)
    {
        $this->newsApiService = $newsApiService;
    }

    #[Route('/news', name: 'news')]
    public function index(Request $request): Response
    {
       $country = $request->query->get('country', 'us'); 

       
            $articles = $this->newsApiService->getTopHeadlines($country);

            return $this->render('news/index.html.twig', [
                'articles' => $articles['articles'], 
                'selectedCountry' => $country,
            ]);
    }
}
