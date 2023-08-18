<?php

declare(strict_types=1);

namespace App\Message;

use App\Entity\Company;
use App\Entity\Turnover;
use App\Exception\CompanyNotFoundException;
use App\Resolver\CompanyInformationResolver;
use App\Service\CompanyService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CompanyMessageHandler
{
    public function __construct(
        private readonly CompanyService $companyService,
        private readonly CompanyInformationResolver $resolver,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function __invoke(CompanyMessage $companyMessage): void
    {
        try {
            $registrationCode = $companyMessage->getRegistrationCode();
            $companyInfo = $this->resolver->resolve(
                registrationCode: $registrationCode,
            );

            $company = Company::fromArray($companyInfo['details']);

            $this->companyService->saveCompanies($company);

            foreach ($companyInfo['turnover'] as $turnover) {
                $turnoverInfo = Turnover::fromArray($turnover);

                $this->companyService->addTurnover(
                    company: $company,
                    turnover: $turnoverInfo,
                );
            }
            $this->logger->notice(
                message: "Company info parsed success: Registration code $registrationCode"
            );
        } catch (CompanyNotFoundException $exception) {
            $this->logger->error(
                message: $exception->getMessage(),
                context: $exception->getContext(),
            );
        }
    }
}
