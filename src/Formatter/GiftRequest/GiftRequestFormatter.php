<?php declare(strict_types=1);

namespace App\Formatter\GiftRequest;

use App\DTO\GiftRequest\GiftRequestDTO;
use App\DTO\GiftRequest\NewGiftRequestDTO;
use App\Entity\GiftRequest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class GiftRequestFormatter
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
    ){}

    /**
     * Converts a GiftRequest model to a GiftRequestDTO.
     */
    public function fromModel(GiftRequest $giftRequest): GiftRequestDTO
    {
        return new GiftRequestDTO(
            id: $giftRequest->getId() ?? 0,
            name: $giftRequest->getName() ?? '',
            description: $giftRequest->getDescription() ?? '',
            editPath: $this->urlGenerator->generate('edit_gift_request', ['id' => $giftRequest->getId()]),
            deletePath: $this->urlGenerator->generate('api_v1_delete_gift_request', ['id' => $giftRequest->getId()]),
        );
    }

    /**
     * Converts a list of GiftRequest models to an array.
     *
     * @param GiftRequest[] $giftRequests An array of GiftRequest models.
     * @return GiftRequestDTO[]
     */
    public function fromModelList(array $giftRequests): array
    {
        $requests = [];
        foreach ($giftRequests as $request) {
            $requests[] = $this->fromModel($request);
        }

        return $requests;
    }

    public function newGiftRequest(Request $request): NewGiftRequestDTO
    {
        $payload = json_decode($request->getContent(), false);

        return new NewGiftRequestDTO(
            name: $payload->name ?? '',
            description: $payload->description ?? '',
        );
    }

    public function editGiftRequest(Request $request): GiftRequestDTO
    {
        $payload = json_decode($request->getContent(), false);

        return new GiftRequestDTO(
            id: $payload->id ?? 0,
            name: $payload->name ?? '',
            description: $payload->description ?? '',
            editPath: $payload->editPath ?? '',
            deletePath: $payload->deletePath ?? '',
        );
    }
}