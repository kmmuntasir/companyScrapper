<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Company;
use App\Entity\Turnover;
use App\Repository\CompanyRepository;
use App\Repository\TurnoverRepository;
use Doctrine\ORM\EntityManagerInterface;

class CompanyService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly CompanyRepository $companyRepository,
        private readonly TurnoverRepository $turnoverRepository,
    ) {
    }

    public function listCompanies(string $registrationCode, int $offset): array
    {
        if ('' !== $registrationCode) {
            return $this->companyRepository->findByRegistrationCode($registrationCode);
        }
        return $this->companyRepository->findAllPaginated($offset);
    }

    public function listTurnovers(Company $company): array
    {
        return $this->turnoverRepository->findByCompany($company);
    }

    public function saveCompanies(Company $company): Company
    {
        $this->entityManager->persist($company);
        $this->entityManager->flush();
        return $company;
    }

    public function addTurnover(
        Company $company,
        Turnover $turnover,
    ): Turnover {
        $turnover->setCompany($company);
        $this->entityManager->persist($turnover);
        $this->entityManager->flush();
        return $turnover;
    }

    public function getCompanyById(int $id): ?Company
    {
        return $this->companyRepository->find($id);
    }

    public function deleteCompany(Company $company): void
    {
        foreach ($company->getTurnovers() as $turnover) {
            $this->turnoverRepository->remove($turnover, true);
        }
        $this->companyRepository->remove($company, true);
    }

    public function getTurnoverById(int $id): ?Turnover
    {
        return $this->turnoverRepository->find($id);
    }

    public function deleteTurnover(Turnover $turnover): void
    {
        $this->turnoverRepository->remove($turnover, true);
    }
}
