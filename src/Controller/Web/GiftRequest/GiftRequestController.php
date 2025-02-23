<?php declare(strict_types=1);

namespace App\Controller\Web\GiftRequest;

use App\Entity\User;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use App\Entity\GiftRequest;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/gift-requests')]
class GiftRequestController extends AbstractController
{

    #[Route(path:'', methods: 'GET', name: 'gift_requests')]
    public function index(
        #[CurrentUser] ?User $user,
        GiftRequestFormatter $giftRequestFormatter
    ): Response {
        
        if (!$user) {
            throw $this->createAccessDeniedException();
        }

        $giftRequests = $user->getGiftRequests()->toArray();
        
        return $this->render('gift-request/index.html.twig', [
            'giftRequests' => $giftRequestFormatter->fromModelList($giftRequests),
            'addGiftRequestURL' => $this->generateUrl('add_gift_request'),
        ]);
    }

    #[Route(path: '/add', methods: ['POST'], name: 'add_gift_request')]
    public function handleAddGiftRequest(
        Request $request,
        #[CurrentUser] ?User $user,
        EntityManagerInterface $entityManager,
        GiftRequestFormatter $giftFormatter,
    ): Response {
        
        $giftData = $giftFormatter->newGiftRequest(($request));

        $newGiftRequest = new GiftRequest();
        $newGiftRequest->setName($giftData->name);
        $newGiftRequest->setDescription($giftData->description);
        $newGiftRequest->setOwner($user);
        $newGiftRequest->setFulfilled(false);

        $entityManager->persist($newGiftRequest);
        $entityManager->flush();

        return $this->json($giftFormatter->fromModel($newGiftRequest), Response::HTTP_CREATED);
    }

    #[Route(
        path: '/{id}/edit', 
        methods: ['GET'], 
        name: 'edit_gift_request')
    ]
    public function editGiftRequest(
        int $id,
        EntityManagerInterface $entityManager,
        GiftRequestFormatter $giftFormatter
    ): Response {
        
        $giftRequest = $entityManager->getRepository(GiftRequest::class)->find($id);

        if(!$giftRequest) {
            throw new EntityNotFoundException('Gift Request not found');
        }

        return $this->render('gift-request/edit/edit.html.twig', [
            'updateURL' => $this->generateUrl(self::class.'::handleEditGiftRequest', ['id' => $giftRequest->getId()]),
            'giftRequest' => $giftFormatter->fromModel($giftRequest),
        ]);
    }

    #[Route(
        path: '/{id}/edit', 
        methods: 'POST', 
        name: 'update_gift_request'
    )]
    public function handleEditGiftRequest(
        int $id,
        Request $request,
        EntityManagerInterface $entityManager,
    ): Response {
        
        $giftRequest = $entityManager->getRepository(GiftRequest::class)->findOrFail($id);
        
        $giftRequest->setName($request->request->get('name'));
        $giftRequest->setDescription($request->request->get('description'));

        $entityManager->flush();

        return $this->json([
            'success' => true,
            'message' => 'Gift Request Updated'
        ], Response::HTTP_OK);
    }

    #[Route(path: '/{id}/delete', methods: 'DELETE', name: 'delete_gift_request')]
    public function handleDeleteGiftRequest(
        int $id,
        EntityManagerInterface $entityManager
    ): Response {
        
        $giftRequest = $entityManager->getRepository(GiftRequest::class)->find($id);

        $entityManager->remove($giftRequest);
        $entityManager->flush();

        return $this->json(['message' => 'Gift Request Deleted'], Response::HTTP_OK);
    }
}