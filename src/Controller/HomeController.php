<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/')]
    public function index(): Response
    {
        $parameters = base64_encode(json_encode([1,2]));
        return $this->render('home/index.html.twig', [ 'parameters' => $parameters]);
    }
}