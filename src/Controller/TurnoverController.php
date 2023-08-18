<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Turnover;
use App\Service\CompanyService;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

class TurnoverController extends AbstractController
{
    public function __construct(
        private readonly CompanyService $companyService,
        private readonly LoggerInterface $logger,
    ) {
    }

    #[Route(
        path: '/api/turnover/{companyId}',
        name: 'listTurnovers',
        methods: ['GET'],
    )]
    public function listTurnovers(int $companyId): JsonResponse
    {
        $company = $this->companyService->getCompanyById($companyId);

        if (!$company) {
            return new JsonResponse(
                ['error' => 'Invalid ID'],
                Response::HTTP_NOT_FOUND,
            );
        }

        $turnovers = $this->companyService->listTurnovers($company);
        return new JsonResponse($turnovers);
    }

    #[Route(
        path: '/api/turnover/{companyId}',
        name: 'addTurnover',
        methods: ['POST'],
    )]
    public function addTurnover(int $companyId, Request $turnoverInfo): JsonResponse
    {
        $company = $this->companyService->getCompanyById($companyId);

        if (!$company) {
            return new JsonResponse(
                ['error' => 'Invalid ID'],
                Response::HTTP_NOT_FOUND,
            );
        }

        $turnover = Turnover::fromArray($turnoverInfo->toArray());

        try {
            $turnover = $this->companyService->addTurnover($company, $turnover);
            return new JsonResponse($turnover, Response::HTTP_CREATED);
        } catch (Throwable $exception) {
            $this->logger->error(
                message: 'Failed to Add Turnover',
                context: ['error' => $exception->getMessage()],
            );

            [$errorMessage, $status] = match (get_class($exception)) {
                UniqueConstraintViolationException::class => [
                    'Duplicate Year',
                    Response::HTTP_BAD_REQUEST,
                ],
                default => ['Failed to Add Company', Response::HTTP_INTERNAL_SERVER_ERROR],
            };

            return new JsonResponse(
                ['error' => $errorMessage],
                $status,
            );
        }
    }

    #[Route(
        path: '/api/turnover/{id}',
        name: 'deleteTurnover',
        methods: ['DELETE'],
    )]
    public function deleteTurnover(int $id): JsonResponse
    {
        $turnover = $this->companyService->getTurnoverById($id);

        if (!$turnover) {
            return new JsonResponse(
                ['error' => 'Invalid ID'],
                Response::HTTP_NOT_FOUND,
            );
        }

        try {
            $this->companyService->deleteTurnover($turnover);
            return new JsonResponse([], Response::HTTP_NO_CONTENT);
        } catch (Throwable $exception) {
            $this->logger->error(
                message: 'Failed to Delete Turnover',
                context: ['error' => $exception->getMessage()],
            );

            return new JsonResponse(
                ['error' => 'Failed to Delete Turnover'],
                Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }
}
