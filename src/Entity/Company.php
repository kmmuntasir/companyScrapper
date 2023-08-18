<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CompanyRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

#[ORM\Index(columns: ['registration_code'], name: 'registration_code')]
#[ORM\UniqueConstraint(
    name: 'registrationCodeUnique',
    columns: ['registration_code'],
)]
#[ORM\Entity(repositoryClass: CompanyRepository::class)]
class Company implements JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(length: 255)]
    private string $name;

    #[ORM\Column(length: 20)]
    private string $registrationCode;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $vat;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $address = null;

    #[ORM\Column(length: 25, nullable: true)]
    private ?string $mobilePhone = null;

    #[ORM\OneToMany(mappedBy: 'company', targetEntity: Turnover::class)]
    private Collection $turnovers;

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
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getRegistrationCode(): string
    {
        return $this->registrationCode;
    }

    /**
     * @param string $registrationCode
     */
    public function setRegistrationCode(string $registrationCode): self
    {
        $this->registrationCode = $registrationCode;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getVat(): ?string
    {
        return $this->vat;
    }

    /**
     * @param string|null $vat
     */
    public function setVat(?string $vat): self
    {
        $this->vat = $vat;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAddress(): ?string
    {
        return $this->address;
    }

    /**
     * @param string|null $address
     */
    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getMobilePhone(): ?string
    {
        return $this->mobilePhone;
    }

    /**
     * @param string|null $mobilePhone
     */
    public function setMobilePhone(?string $mobilePhone): self
    {
        $this->mobilePhone = $mobilePhone;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getTurnovers(): Collection
    {
        return $this->turnovers;
    }

    /**
     * @param Collection $turnovers
     */
    public function setTurnovers(Collection $turnovers): self
    {
        $this->turnovers = $turnovers;

        return $this;
    }
    
    public static function fromArray(array $companyInfo): self
    {
        $instance = new static();
        
        return $instance->setName($companyInfo['name'])
            ->setRegistrationCode($companyInfo['registrationCode'])
            ->setVat($companyInfo['vat'] ?? null)
            ->setAddress($companyInfo['address'] ?? null)
            ->setMobilePhone($companyInfo['mobilePhone'] ?? null)
        ;
    }

    public function updateFromArray(array $companyInfo): void
    {
        $this->setName($companyInfo['name'] ?? $this->name);
        $this->setRegistrationCode($companyInfo['registrationCode'] ?? $this->registrationCode);
        $this->setVat($companyInfo['vat'] ?? $this->vat);
        $this->setAddress($companyInfo['address'] ?? $this->address);
        $this->setMobilePhone($companyInfo['mobilePhone'] ?? $this->mobilePhone);
    }

    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'registrationCode' => $this->getRegistrationCode(),
            'vat' => $this->getVat(),
            'address' => $this->getAddress(),
            'mobilePhone' => $this->getMobilePhone(),
        ];
    }
}
