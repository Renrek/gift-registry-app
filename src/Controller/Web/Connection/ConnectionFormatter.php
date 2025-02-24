<?php declare(strict_types=1);

namespace App\Controller\Web\Connection;

use App\Controller\User\DTOs\UserDTO;
use App\Entity\Connection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ConnectionFormatter
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
    ){}

    /**
     * Converts a Connection model to an array.
     *
     * @param Connection $connection The Connection model.
     * @return array<string, mixed> The formatted connection data.
     */
    public function fromModel(Connection $connection): array
    {
        $user = $connection->getUser();
        $connectedUser = $connection->getConnectedUser();

        if (!$user) {
            throw new \LogicException('The user is not an instance of User.');
        }

        return [
            'id' => $connection->getId(),
            'userId' => $user->getId(),
            'email' => $user->getEmail(),
            'confirmed' => $connection->isConfirmed(),
            'confirmUrl' => $this->urlGenerator->generate('confirm_connection', ['id' => $connection->getId()]),
            'deleteUrl' => $this->urlGenerator->generate('delete_connection', ['id' => $connection->getId()]),
        ];
    }
}