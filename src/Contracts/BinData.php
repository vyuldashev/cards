<?php

declare(strict_types=1);

namespace Vyuldashev\Cards\Contracts;

interface BinData
{
    /**
     * Get type.
     *
     * @return int
     */
    public function getType(): int;

    /**
     * Get country.
     *
     * @return string
     */
    public function getCountry(): string;

    /**
     * Get bank name.
     *
     * @return string
     */
    public function getBank(): string;
}
