<?php
// src/Controller/LuckyController.php
declare(strict_types=1);

namespace App\Helper;
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\StreamedJsonResponse;
use App\Service\Funciones;

class LuckyController extends AbstractController
{
    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
        $this->F = new  Funciones;
    }
    #[Route('/Arignon/wc/back/lucky/number/')]
    public function number(): Response
    {
        $number = random_int(0, 100);

        return $this->render('lucky/number.html.twig', [
            'number' => $number, 'frase' =>
            $this->F->hola()
        ]);
    }
    #[Route('/Arignon/wc/back/art/{p1?}/{p2?}/{p3?}/{p4?}')]
    public function art($p1,$p2,$p3,$p4): Response
    {
        $url = "https://www.arignon.com.ar/back/api/buscart/$p1";      
        if ( isset($p2) ) $url.= "/$p2";  
        if ( isset($p3) ) $url.= "/$p3";  
        if ( isset($p4) ) $url.= "/$p4";  
        $response = $this->client->request('GET', $url, [
            'query' => [
                'limitTo' => '[nerdy]',
                'escape' => 'ee',
            ],
        ]);
        $parsedResponse = $response->toArray();
        return new StreamedJsonResponse($response->toArray());
        return new Response( $response->toJson() );

    }
    
    
}