<?php declare(strict_types=1);

namespace App\Feature\ConnectionPanel\Formatter;

use App\Entity\Connection;
use App\Entity\User;
use App\Feature\ConnectionPanel\DTO\ConnectionPanelItemDTO;
use App\Feature\ConnectionPanel\Enum\ConfirmStatus;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ConnectionPanelFormatter
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private Security $security,
    ) {}

    /**
     * Converts a list of Connection models to an array.
     * 
     * @param Connection[] $connections An array of Connection models.
     * @return ConnectionPanelItemDTO[]
     */
    public function fromModelList(array $connections): array
    {
        $requests = [];
        foreach ($connections as $connection) {
            $requests[] = $this->fromModels($connection);
        }

        return $requests;
    }

    public function fromModels(Connection $connection): ConnectionPanelItemDTO
    {
        $user = $this->security->getUser();

        if (!$user instanceof User || $user == null) {
            throw new \LogicException('The user is not logged in or is not an instance of User.');
        }

        if ($connection->getUser() === null || $connection->getConnectedUser() === null) {
            throw new \LogicException('The connection does not have a user or connected user.');
        }

        $isConnectionInitiator = $connection->getUser()->getId() === $user->getId();

        $status = $connection->isConfirmed() ? ConfirmStatus::CONFIRMED : ConfirmStatus::NOT_CONFIRMED;
        if ($isConnectionInitiator) {
            // If the user is the initiator of the connection, the status is pending if the connection is not confirmed.
            if (!$connection->isConfirmed()) {
                $status = ConfirmStatus::PENDING;
            }
            $connectedUser = $connection->getConnectedUser();
        } else {
            $connectedUser = $connection->getUser();
        }

        return new ConnectionPanelItemDTO(
            id: $connection->getId() ?? 0,
            userId: $connectedUser->getId() ?? 0,
            email: $connectedUser->getEmail() ?? '',
            status: $status,
            confirmUrl: $this->urlGenerator->generate('api_v1_confirm_connection', ['connectionId' => $connection->getId()]),
            deleteUrl: $this->urlGenerator->generate('api_v1_delete_connection', ['connectionId' => $connection->getId()]),
        );
    }
}