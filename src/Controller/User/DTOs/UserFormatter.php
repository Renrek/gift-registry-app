<?php declare(strict_types=1);

namespace App\Controller\User\DTOs;

use App\Entity\User;

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
        return new UserDTO(
            id: $user->getId(),
            email: $user->getEmail(),
        );
    }
}