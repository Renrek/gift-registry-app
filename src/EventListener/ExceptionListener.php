<?php declare(strict_types=1);

namespace App\EventListener;

use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[AsEventListener(event: KernelEvents::EXCEPTION)]
class ExceptionListener
{
    public function __invoke(ExceptionEvent $event): void
    {
        // Only intercept API requests — web routes use Symfony's default HTML error pages
        if (!str_starts_with($event->getRequest()->getPathInfo(), '/api/')) {
            return;
        }

        $exception = $event->getThrowable();

        $response = match (true) {
            $exception instanceof EntityNotFoundException => new JsonResponse(
                ['error' => $exception->getMessage()],
                Response::HTTP_NOT_FOUND
            ),
            $exception instanceof AccessDeniedException => new JsonResponse(
                ['error' => $exception->getMessage()],
                Response::HTTP_FORBIDDEN
            ),
            default => null,
        };

        if ($response !== null) {
            $event->setResponse($response);
        }
    }
}
