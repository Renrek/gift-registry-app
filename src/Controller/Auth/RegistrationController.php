<?php declare(strict_types=1);

namespace App\Controller\Auth;

use App\Controller\Auth\DTOs\UserRegistrationFormatter;
use App\Entity\Invitation;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;


class RegistrationController extends AbstractController
{
    #[Route(path: '/registration', methods: 'GET')]
    public function index(): Response
    {
        return $this->render('registration/index.html.twig', []);
    }


    #[Route(path: '/register', methods: 'POST')]
    public function handleRegistration(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager,
        UserRegistrationFormatter $registrationFormatter,
    ): Response {
        $userData = $registrationFormatter->fromRequest($request);
        $invitationCode = $request->request->get('invitation_code');
        if (!$userData->email || !$userData->password  || !$invitationCode) {
            return new Response('Not enough information', Response::HTTP_BAD_REQUEST);
        }
        $invitation = $entityManager->getRepository(Invitation::class)->findOneBy(['invitationCode' => $invitationCode, 'used' => false]);

        if (!$invitation || $invitation->getEmail() !== $userData->email) {
            return new Response('Invalid or used invitation', Response::HTTP_BAD_REQUEST);
        }

        $user = new User();
        $user->setEmail($userData->email);
        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $userData->password,
        );
        $user->setPassword($hashedPassword);
        $user->setInvitedBy($invitation->getInviter());
        $entityManager->persist($user);
        $entityManager->flush();
        
        $invitation->setUsed(true);
        $entityManager->persist($invitation);

        $entityManager->flush();

        return new Response('User registered successfully', Response::HTTP_CREATED);
    }
}