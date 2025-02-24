<?php declare(strict_types=1);

namespace App\Controller\Web\Registration;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/registration', methods: 'GET')]
class RegistrationController extends AbstractController
{
    #[Route(path: '', methods: 'GET')]
    public function index(): Response
    {
        return $this->render('registration/index.html.twig', []);
    }

}