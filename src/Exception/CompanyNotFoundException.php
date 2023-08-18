<?php

declare(strict_types=1);

namespace App\Exception;

use Exception;

class CompanyNotFoundException extends Exception
{
    private array $context = [];

    /**
     * @return array
     */
    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * @param array $context
     */
    public function setContext(array $context): self
    {
        $this->context = $context;

        return $this;
    }
}
