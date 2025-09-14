<?php declare(strict_types=1);

namespace App\Controller\Web\GiftReservation;

use App\Entity\User;
use App\Formatter\GiftRequest\GiftRequestFormatter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/gift-reservations')]
class GiftReservationController extends AbstractController
{
    #[Route(path: '', methods: 'GET', name: 'gift_reservations')]
    public function index(
        #[CurrentUser] ?User $user,
        EntityManagerInterface $entityManager,
        GiftRequestFormatter $giftRequestFormatter
    ): Response {
        // TODO: Fetch available gift requests that are reserved (not owned by $user, already reserved by $user)
        
        return $this->render('reservation/index.html.twig', [
            'giftReservations' => '',
        ]);
    }

}
