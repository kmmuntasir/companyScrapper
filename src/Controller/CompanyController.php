<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Company;
use App\Message\CompanyMessage;
use App\Service\CompanyService;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

class CompanyController extends AbstractController
{
    public function __construct(
        private readonly CompanyService $companyService,
        private readonly MessageBusInterface $messageBus,
        private readonly LoggerInterface $logger,
    ) {
    }

    #[Route(
        path: '/api/company/{offset}',
        name: 'listCompanies',
        methods: ['GET'],
    )]
    public function listCompanies(Request $request, int $offset=0): JsonResponse
    {
        $term = $request->query->get('search', '');
        $companies = $this->companyService->listCompanies($term, $offset);
        return new JsonResponse($companies);
    }

    #[Route(
        path: '/api/company',
        name: 'addCompany',
        methods: ['POST'],
    )]
    public function addCompany(Request $companyInfo): JsonResponse
    {
        $company = Company::fromArray($companyInfo->toArray());

        try {
            $company = $this->companyService->saveCompanies($company);
            return new JsonResponse($company, Response::HTTP_CREATED);
        } catch (Throwable $exception) {
            $this->logger->error(
                message: 'Failed to Add Company',
                context: ['error' => $exception->getMessage()],
            );

            [$errorMessage, $status] = match (get_class($exception)) {
                UniqueConstraintViolationException::class => [
                    'Duplicate Registration Code',
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
        path: '/api/company/{id}',
        name: 'updateCompany',
        methods: ['PATCH'],
    )]
    public function updateCompany(int $id, Request $updatedCompanyInfo): JsonResponse
    {
        $company = $this->companyService->getCompanyById($id);

        if (!$company) {
            return new JsonResponse(
                ['error' => 'Invalid ID'],
                Response::HTTP_NOT_FOUND,
            );
        }

        $company->updateFromArray($updatedCompanyInfo->toArray());

        try {
            $company = $this->companyService->saveCompanies($company);
            return new JsonResponse($company, Response::HTTP_ACCEPTED);
        } catch (Throwable $exception) {
            $this->logger->error(
                message: 'Failed to Update Company',
                context: ['error' => $exception->getMessage()],
            );

            [$errorMessage, $status] = match (get_class($exception)) {
                UniqueConstraintViolationException::class => [
                    'Duplicate Registration Code',
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
        path: '/api/company/{id}',
        name: 'deleteCompany',
        methods: ['DELETE'],
    )]
    public function deleteCompany(int $id): JsonResponse
    {
        $company = $this->companyService->getCompanyById($id);

        if (!$company) {
            return new JsonResponse(
                ['error' => 'Invalid ID'],
                Response::HTTP_NOT_FOUND,
            );
        }

        try {
            $this->companyService->deleteCompany($company);
            return new JsonResponse([], Response::HTTP_NO_CONTENT);
        } catch (Throwable $exception) {
            $this->logger->error(
                message: 'Failed to Delete Company',
                context: ['error' => $exception->getMessage()],
            );

            return new JsonResponse(
                ['error' => 'Failed to Delete Company'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    #[Route(
        path: '/api/search',
        name: 'search',
        methods: ['POST'],
    )]
    public function search(
        Request $request,
    ): JsonResponse {
        $codes = $request->toArray();

        foreach ($codes as $registrationCode) {
            $this->messageBus->dispatch(
                new CompanyMessage($registrationCode),
            );
        }

        return new JsonResponse(null, Response::HTTP_OK);
    }
}
