<?php declare(strict_types=1);

namespace App\Controller\Home;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path:'')]
class HomeController extends AbstractController
{
    #[Route(path:'', methods: 'GET')]
    public function index(): Response
    {

        $parameters = base64_encode(json_encode([1,2]));
        $securityContext = $this->container->get('security.authorization_checker');
        $isLoggedin = $securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED');
        return $this->render('home/index.html.twig', [ 
            'parameters' => $parameters,
            'isLoggedin' => $isLoggedin,
        ]);
    }
}