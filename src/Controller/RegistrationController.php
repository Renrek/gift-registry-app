<?php declare(strict_types=1);

namespace App\Controller;

use App\Dto\Formatter\UserRegistrationFormatter;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/registration')]
class RegistrationController extends AbstractController
{
    #[Route(path: '', methods: 'GET')]
    public function index(): Response
    {
        return $this->render('registration/index.html.twig', []);
    }


    #[Route(path: '', methods: 'POST')]
    public function handleRegistration(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager,
        UserRegistrationFormatter $registrationFormatter,
    ): Response {
        $userData = $registrationFormatter->fromRequest($request);
        if (!$userData->email || !$userData->password) {
            return new Response('Not enough information', Response::HTTP_BAD_REQUEST);
        }
        $user = new User();
        $user->setEmail($userData->email);
        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $userData->password,
        );
        $user->setPassword($hashedPassword);
        $entityManager->persist($user);
        $entityManager->flush();
        return new Response('', Response::HTTP_CREATED);
    }
}