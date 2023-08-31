<?php

namespace App\Exception;

use Exception;

class ResolveFailedException extends Exception
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