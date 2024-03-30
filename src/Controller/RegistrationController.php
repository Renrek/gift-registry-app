<?php declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/registration')]
class RegistrationController extends AbstractController
{
    #[Route(path: '', methods: 'GET')]
    public function index(UserPasswordHasherInterface $passwordHasher): Response
    {
        return $this->render('registration/index.html.twig', []);
    }
}