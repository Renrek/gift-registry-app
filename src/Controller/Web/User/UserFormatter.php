<?php declare(strict_types=1);

namespace App\Controller\Web\User;

use App\Controller\Web\User\DTOs\UserDTO;
use App\Entity\User;
use DomainException;

class UserFormatter
{
    /**
     * @param User[] $users
     * @return UserDTO[]
     */
    public function fromEntityList(array $users): array
    {
        return array_map(fn(User $user) => $this->fromEntity($user), $users);
    }

    public function fromEntity(User $user): UserDTO
    {
        if (!$user->getId() || !$user->getEmail()) {
            throw new DomainException('User must have an ID and email.');
        }
        
        return new UserDTO(
            id: $user->getId(),
            email: $user->getEmail(),
        );
    }
}