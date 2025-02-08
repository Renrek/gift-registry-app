<?php declare(strict_types=1);

namespace App\Controller\Invitation;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\User;
use App\Service\InvitationService;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path:'/invitations')]
class InvitationController extends AbstractController
{
    #[Route(path: '/create', methods: ['POST'], name: 'create_invitation')]
    public function create(Request $request, InvitationService $invitationService): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return new Response('User not authenticated', Response::HTTP_UNAUTHORIZED);
        }

        $payload = json_decode($request->getContent(), false);
        $email = $payload->email;
        
        if (!$email) {
            return new Response('Email is required', Response::HTTP_BAD_REQUEST);
        }

        $invitation = $invitationService->createInvitation($email);

        return new Response('Invitation created successfully', Response::HTTP_CREATED);
    }
}