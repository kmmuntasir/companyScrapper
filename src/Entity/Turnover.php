<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\TurnoverRepository;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

#[ORM\Index(columns: ['company_id'], name: 'company_id')]
#[ORM\UniqueConstraint(
    name: 'companyIdWithYearUnique',
    columns: ['company_id', 'year'],
)]
#[ORM\Entity(repositoryClass: TurnoverRepository::class)]
class Turnover implements JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Company::class, inversedBy: 'turnovers')]
    private Company $company;

    #[ORM\Column]
    private int $year;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $nonCurrentAssets = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $currentAssets = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $equity = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $liabilities = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $salesRevenue = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $profitBeforeTaxes = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $profitBeforeTaxesMargin = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $netProfit = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $netProfitMargin = null;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return Company
     */
    public function getCompany(): Company
    {
        return $this->company;
    }

    /**
     * @param Company $company
     */
    public function setCompany(Company $company): self
    {
        $this->company = $company;

        return $this;
    }

    /**
     * @return int
     */
    public function getYear(): int
    {
        return $this->year;
    }

    /**
     * @param int $year
     */
    public function setYear(int $year): self
    {
        $this->year = $year;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getNonCurrentAssets(): ?string
    {
        return $this->nonCurrentAssets;
    }

    /**
     * @param string|null $nonCurrentAssets
     */
    public function setNonCurrentAssets(?string $nonCurrentAssets): self
    {
        $this->nonCurrentAssets = $nonCurrentAssets;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCurrentAssets(): ?string
    {
        return $this->currentAssets;
    }

    /**
     * @param string|null $currentAssets
     */
    public function setCurrentAssets(?string $currentAssets): self
    {
        $this->currentAssets = $currentAssets;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getEquity(): ?string
    {
        return $this->equity;
    }

    /**
     * @param string|null $equity
     */
    public function setEquity(?string $equity): self
    {
        $this->equity = $equity;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLiabilities(): ?string
    {
        return $this->liabilities;
    }

    /**
     * @param string|null $liabilities
     */
    public function setLiabilities(?string $liabilities): self
    {
        $this->liabilities = $liabilities;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSalesRevenue(): ?string
    {
        return $this->salesRevenue;
    }

    /**
     * @param string|null $salesRevenue
     */
    public function setSalesRevenue(?string $salesRevenue): self
    {
        $this->salesRevenue = $salesRevenue;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getProfitBeforeTaxes(): ?string
    {
        return $this->profitBeforeTaxes;
    }

    /**
     * @param string|null $profitBeforeTaxes
     */
    public function setProfitBeforeTaxes(?string $profitBeforeTaxes): self
    {
        $this->profitBeforeTaxes = $profitBeforeTaxes;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getProfitBeforeTaxesMargin(): ?string
    {
        return $this->profitBeforeTaxesMargin;
    }

    /**
     * @param string|null $profitBeforeTaxesMargin
     */
    public function setProfitBeforeTaxesMargin(?string $profitBeforeTaxesMargin): self
    {
        $this->profitBeforeTaxesMargin = $profitBeforeTaxesMargin;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getNetProfit(): ?string
    {
        return $this->netProfit;
    }

    /**
     * @param string|null $netProfit
     */
    public function setNetProfit(?string $netProfit): self
    {
        $this->netProfit = $netProfit;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getNetProfitMargin(): ?string
    {
        return $this->netProfitMargin;
    }

    /**
     * @param string|null $netProfitMargin
     */
    public function setNetProfitMargin(?string $netProfitMargin): self
    {
        $this->netProfitMargin = $netProfitMargin;

        return $this;
    }

    public static function fromArray(array $turnover): self
    {
        $instance = new static();

        return $instance->setYear((int) $turnover['year'])
            ->setNonCurrentAssets($turnover['nonCurrentAssets'])
            ->setCurrentAssets($turnover['currentAssets'])
            ->setEquity($turnover['equity'])
            ->setLiabilities($turnover['liabilities'])
            ->setSalesRevenue($turnover['salesRevenue'])
            ->setProfitBeforeTaxes($turnover['profitBeforeTaxes'])
            ->setProfitBeforeTaxesMargin($turnover['profitBeforeTaxesMargin'])
            ->setNetProfit($turnover['netProfit'])
            ->setNetProfitMargin($turnover['netProfitMargin'])
        ;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->getId(),
            'year' => $this->getYear(),
            'nonCurrentAssets' => $this->getNonCurrentAssets(),
            'currentAssets' => $this->getCurrentAssets(),
            'equity' => $this->getEquity(),
            'liabilities' => $this->getLiabilities(),
            'salesRevenue' => $this->getSalesRevenue(),
            'profitBeforeTaxes' => $this->getProfitBeforeTaxes(),
            'profitBeforeTaxesMargin' => $this->getProfitBeforeTaxesMargin(),
            'netProfit' => $this->getNetProfit(),
            'netProfitMargin' => $this->getNetProfitMargin(),
        ];
    }
}
