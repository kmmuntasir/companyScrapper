<?php

declare(strict_types=1);

namespace App\Message;

class CompanyMessage
{
    public function __construct(
        private readonly string $registrationCode,
    ) {
    }

    public function getRegistrationCode(): string
    {
        return $this->registrationCode;
    }
}
